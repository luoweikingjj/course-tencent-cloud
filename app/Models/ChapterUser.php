<?php

namespace App\Models;

class ChapterUser extends Model
{

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 课程编号
     *
     * @var int
     */
    public $course_id = 0;

    /**
     * 章节编号
     *
     * @var int
     */
    public $chapter_id = 0;

    /**
     * 用户编号
     *
     * @var int
     */
    public $user_id = 0;

    /**
     * 计划编号
     *
     * @var int
     */
    public $plan_id = 0;

    /**
     * 学习时长（秒）
     *
     * @var int
     */
    public $duration = 0;

    /**
     * 播放位置（秒）
     *
     * @var int
     */
    public $position = 0;

    /**
     * 学习进度（％）
     *
     * @var int
     */
    public $progress = 0;

    /**
     * 消费标识
     *
     * @var int
     */
    public $consumed = 0;

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
        return 'kg_chapter_user';
    }

    public function beforeCreate()
    {
        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        $this->update_time = time();
    }

}