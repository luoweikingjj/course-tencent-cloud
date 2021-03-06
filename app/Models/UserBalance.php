<?php

namespace App\Models;

class UserBalance extends Model
{

    /**
     * 用户编号（主键）
     *
     * @var int
     */
    public $user_id = 0;

    /**
     * 可用现金（元）
     *
     * @var float
     */
    public $cash = 0.00;

    /**
     * 可用积分
     *
     * @var int
     */
    public $point = 0;

    /**
     * 创建时间
     *
     * @var int
     */
    public $create_time = 0;

    /**
     * 更新时间
     *
     * @var int
     */
    public $update_time = 0;

    public function getSource(): string
    {
        return 'kg_user_balance';
    }

    public function beforeCreate()
    {
        $this->create_time = time();
    }

    public function beforeSave()
    {
        $this->update_time = time();
    }

}