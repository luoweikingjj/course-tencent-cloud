<?php

namespace App\Models;

use App\Caches\MaxHelpId as MaxHelpIdCache;
use Phalcon\Mvc\Model\Behavior\SoftDelete;

class Help extends Model
{

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 分类编号
     *
     * @var int
     */
    public $category_id = 0;

    /**
     * 标题
     *
     * @var string
     */
    public $title = '';

    /**
     * 内容
     *
     * @var string
     */
    public $content = '';

    /**
     * 优先级
     *
     * @var int
     */
    public $priority = 100;

    /**
     * 发布标识
     *
     * @var int
     */
    public $published = 0;

    /**
     * 删除标识
     *
     * @var int
     */
    public $deleted = 0;

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
        return 'kg_help';
    }

    public function initialize()
    {
        parent::initialize();

        $this->addBehavior(
            new SoftDelete([
                'field' => 'deleted',
                'value' => 1,
            ])
        );
    }

    public function beforeCreate()
    {
        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        if ($this->deleted == 1) {
            $this->published = 0;
        }

        $this->update_time = time();
    }

    public function afterCreate()
    {
        $cache = new MaxHelpIdCache();

        $cache->rebuild();
    }

}