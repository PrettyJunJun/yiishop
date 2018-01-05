<?php

namespace backend\models;


use Yii;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\helpers\Json;

/**
 * This is the model class for table "goods_category".
 *
 * @property integer $id
 * @property integer $tree
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $name
 * @property integer $parent_id
 * @property string $intro
 */
class GoodsCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tree', 'lft', 'rgt', 'depth', 'parent_id'], 'integer'],
            [['intro'], 'string'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tree' => '树ID',
            'lft' => '左值',
            'rgt' => '右值',
            'depth' => '层级',
            'name' => '商品名称',
            'parent_id' => '上级分类ID',
            'intro' => '简介',
        ];
    }

    public function behaviors()
    {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
                // 'leftAttribute' => 'lft',
                // 'rightAttribute' => 'rgt',
                // 'depthAttribute' => 'depth',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }

    //>>获取分类数据 作为ztree的节点数据
    public static function getNodes()
    {
        $nodes = self::find()->select(['id', 'parent_id', 'name'])->asArray()->all();
        array_unshift($nodes, ['id' => 0, 'parent_id' => 0, 'name' => '【顶级分类】']);
        return Json::encode($nodes);
    }

    //>>前台分类展示
    public static function CategoryShow()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $html = $redis->get('goods_category');

        if (!$html) {
            $tops = GoodsCategory::find()->where(['parent_id' => 0])->all();
            foreach ($tops as $value => $top) {
                $html .= '<div class="cat ' . ($value == 0 ? 'item1' : '') . '">';
                $html .= '<h3><a href="' . \yii\helpers\Url::to(['goods-list/index', 'id' => $top->id]) . '">' . $top->name . '</a><b></b></h3>';
                $seconds = GoodsCategory::find()->where(['parent_id' => $top->id])->all();
                $two[$top->id] = $seconds;
                $html .= '   <div class="cat_detail">';
                foreach ($two[$top->id] as $values => $second) {
                    $html .= '<dl ' . ($values == 0 ? 'class="dl_1st"' : '') . '>';
                    $html .= ' <dt><a href="' . \yii\helpers\Url::to(['goods-list/index', 'id' => $second->id]) . '">' . $second->name . '</a></dt>';
                    $thirds = GoodsCategory::find()->where(['parent_id' => $second->id])->all();
                    $three[$second->id] = $thirds;
                    foreach ($three[$second->id] as $third) {
                        $html .= '<dd>';
                        $html .= '<a href="' . \yii\helpers\Url::to(['goods-list/index', 'id' => $third->id]) . '">' . $third->name . '</a>';
                        $html .= '</dd>';
                    }
                    $html .= ' </dl>';
                }
                $html .= '</div>';

                $html .= '</div>';
            }
            $redis->set('goods_Category', $html, 24 * 3600);
        }


        return $html;
    }
}
