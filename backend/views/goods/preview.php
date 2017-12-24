<table class="table">
    <?php foreach ($gallery as $g): ?>
        <?php echo \yii\bootstrap\Carousel::widget([
            'items' => [
                // 包含图片和字幕的格式
                [
                    'content' => '<img src="{$g->path}"/>',
                    'caption' => '<h4>哈哈哈哈</h4><p>这是什么鬼</p>',
                    //'options' => [...],       //配置对应的样式
                ],
            ]
        ]);
        ?>
    <?php endforeach; ?>
    <?php foreach ($content as $c): ?>
        <tr>
            <td>
                <?= $c->content ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>