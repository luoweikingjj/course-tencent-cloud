<?php

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Models\Course as CourseModel;
use App\Models\PointGift as PointGiftModel;
use App\Models\User as UserModel;
use App\Repos\CourseUser as CourseUserRepo;
use App\Repos\PointRedeem as PointRedeemRepo;
use App\Repos\User as UserRepo;


class PointRedeem extends Validator
{

    public function checkRedeem($id)
    {
        $redeemRepo = new PointRedeemRepo();

        $redeem = $redeemRepo->findById($id);

        if (!$redeem) {
            throw new BadRequestException('point_redeem.not_found');
        }

        return $redeem;
    }

    public function checkPointGift($giftId)
    {
        $validator = new PointGift();

        return $validator->checkPointGift($giftId);
    }

    public function checkIfAllowRedeem(PointGiftModel $gift, UserModel $user)
    {
        $this->checkStock($gift);

        $this->checkRedeemLimit($gift, $user);

        $this->checkPointBalance($gift, $user);

        if ($gift->type == PointGiftModel::TYPE_COURSE) {

            $validator = new Course();

            $course = $validator->checkCourse($gift->attrs['id']);

            $this->checkIfAllowRedeemCourse($course, $user);

        } elseif ($gift->type == PointGiftModel::TYPE_GOODS) {

            $this->checkIfAllowRedeemGoods($user);
        }
    }

    protected function checkIfAllowRedeemCourse(CourseModel $course, UserModel $user)
    {
        if ($course->published == 0) {
            throw new BadRequestException('point_redeem.course_not_published');
        }

        if ($course->market_price == 0) {
            throw new BadRequestException('point_redeem.course_free');
        }

        $courseUserRepo = new CourseUserRepo();

        $courseUser = $courseUserRepo->findCourseUser($course->id, $user->id);

        if ($courseUser && $courseUser->expiry_time > time()) {
            throw new BadRequestException('point_redeem.course_owned');
        }
    }

    protected function checkIfAllowRedeemGoods(UserModel $user)
    {
        $userRepo = new UserRepo();

        $contact = $userRepo->findUserContact($user->id);

        if (!$contact) {
            throw new BadRequestException('point_redeem.no_user_contact');
        }
    }

    protected function checkStock(PointGiftModel $gift)
    {
        if ($gift->stock < 1) {
            throw new BadRequestException('point_redeem.no_enough_stock');
        }
    }

    protected function checkRedeemLimit(PointGiftModel $gift, UserModel $user)
    {
        $redeemRepo = new PointRedeemRepo();

        $count = $redeemRepo->countUserGiftRedeems($user->id, $gift->id);

        if ($count >= $gift->redeem_limit) {
            throw new BadRequestException('point_redeem.reach_redeem_limit');
        }
    }

    protected function checkPointBalance(PointGiftModel $gift, UserModel $user)
    {
        $userRepo = new UserRepo();

        $balance = $userRepo->findUserBalance($user->id);

        if (!$balance || $balance->point < $gift->point) {
            throw new BadRequestException('point_redeem.no_enough_point');
        }
    }

}
