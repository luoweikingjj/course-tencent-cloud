<?php

namespace App\Services\Logic\Notice;

use App\Models\PointRedeem as PointRedeemModel;
use App\Models\Task as TaskModel;
use App\Repos\PointRedeem as PointRedeemRepo;
use App\Repos\User as UserRepo;
use App\Repos\WeChatSubscribe as WeChatSubscribeRepo;
use App\Services\Logic\Notice\Sms\GoodsDeliver as SmsGoodsDeliverNotice;
use App\Services\Logic\Notice\WeChat\GoodsDeliver as WeChatGoodsDeliverNotice;
use App\Services\Logic\Service as LogicService;

class PointGoodsDeliver extends LogicService
{

    public function handleTask(TaskModel $task)
    {
        $wechatNoticeEnabled = $this->wechatNoticeEnabled();
        $smsNoticeEnabled = $this->smsNoticeEnabled();

        if (!$wechatNoticeEnabled && !$smsNoticeEnabled) return;

        $redeemId = $task->item_info['point_redeem']['id'];

        $redeemRepo = new PointRedeemRepo();

        $redeem = $redeemRepo->findById($redeemId);

        $userRepo = new UserRepo();

        $user = $userRepo->findById($redeem->user_id);

        $params = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'goods_name' => $redeem->gift_name,
            'order_sn' => date('YmdHis') . rand(1000, 9999),
            'deliver_time' => time(),
        ];

        $subscribeRepo = new WeChatSubscribeRepo();

        $subscribe = $subscribeRepo->findByUserId($user->id);

        if ($wechatNoticeEnabled && $subscribe) {

            $notice = new WeChatGoodsDeliverNotice();

            return $notice->handle($subscribe, $params);

        } elseif ($smsNoticeEnabled) {

            $notice = new SmsGoodsDeliverNotice();

            return $notice->handle($user, $params);
        }
    }

    public function createTask(PointRedeemModel $redeem)
    {
        $wechatNoticeEnabled = $this->wechatNoticeEnabled();
        $smsNoticeEnabled = $this->smsNoticeEnabled();

        if (!$wechatNoticeEnabled && !$smsNoticeEnabled) return;

        $task = new TaskModel();

        $itemInfo = [
            'point_redeem' => ['id' => $redeem->id],
        ];

        $task->item_id = $redeem->id;
        $task->item_info = $itemInfo;
        $task->item_type = TaskModel::TYPE_NOTICE_POINT_GOODS_DELIVER;
        $task->priority = TaskModel::PRIORITY_MIDDLE;
        $task->status = TaskModel::STATUS_PENDING;

        $task->create();
    }

    public function wechatNoticeEnabled()
    {
        $oa = $this->getSettings('wechat.oa');

        if ($oa['enabled'] == 0) return false;

        $template = json_decode($oa['notice_template'], true);

        $result = $template['goods_deliver']['enabled'] ?? 0;

        return $result == 1;
    }

    public function smsNoticeEnabled()
    {
        $sms = $this->getSettings('sms');

        $template = json_decode($sms['template'], true);

        $result = $template['goods_deliver']['enabled'] ?? 0;

        return $result == 1;
    }

}
