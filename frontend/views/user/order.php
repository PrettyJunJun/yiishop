<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>填写核对订单信息</title>
    <link rel="stylesheet" href="/style/base.css" type="text/css">
    <link rel="stylesheet" href="/style/global.css" type="text/css">
    <link rel="stylesheet" href="/style/header.css" type="text/css">
    <link rel="stylesheet" href="/style/fillin.css" type="text/css">
    <link rel="stylesheet" href="/style/footer.css" type="text/css">

    <script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="/js/cart2.js"></script>

</head>
<body>
<!-- 顶部导航 start -->
<div class="topnav">
    <div class="topnav_bd w990 bc">
        <div class="topnav_left">

        </div>
        <div class="topnav_right fr">
            <ul>
                <li><?= Yii::$app->user->isGuest ? "游客" : Yii::$app->user->identity->username ?>
                    您好！欢迎来到京西！<?= Yii::$app->user->isGuest ? '[<a href="' . \yii\helpers\Url::to(['user/login']) . '">登录</a>][<a href="' . yii\helpers\Url::to(['user/regist']) . '">免费注册</a>]' : '[<a href="' . \yii\helpers\Url::to(['user/logout']) . '">注销</a>]' ?> </li>
                <li class="line">|</li>
                <li><a href="<?= \yii\helpers\Url::to(['user/details']) ?>">我的订单</a></li>
                <li class="line">|</li>
                <li>客户服务</li>

            </ul>
        </div>
    </div>
</div>
<!-- 顶部导航 end -->

<div style="clear:both;"></div>

<!-- 页面头部 start -->
<div class="header w990 bc mt15">
    <div class="logo w990">
        <h2 class="fl"><a href="index.html"><img src="/images/logo.png" alt="京西商城"></a></h2>
        <div class="flow fr flow2">
            <ul>
                <li>1.我的购物车</li>
                <li class="cur">2.填写核对订单信息</li>
                <li>3.成功提交订单</li>
            </ul>
        </div>
    </div>
</div>
<!-- 页面头部 end -->

<div style="clear:both;"></div>

<!-- 主体部分 start -->
<form action="<?= \yii\helpers\Url::to(['user/order']) ?>" method="post">
<div class="fillin w990 bc mt15">

        <div class="fillin_hd">
            <h2>填写并核对订单信息</h2>
        </div>

        <div class="fillin_bd">
            <!-- 收货人信息  start-->
            <div class="address">
                <h3>收货人信息</h3>
                <?php foreach ($address as $addres): ?>
                    <?php $value= \frontend\models\DetailAddress::findOne(['address_id'=>$addres['id']])?>
                    <div class="address_info">
                        <p>
                            <input type="radio" value="<?= isset($addres->id) ? $addres->id : '1' ?>"
                                   name="address_id" <?= isset($addres->default) == 1 ? 'checked="checked"' : '' ?>/>
                            <?= isset($addres->name) ? $addres->name : '' ?>
                            <?= isset($addres->phone) ? $addres->phone : '' ?>
                            <?= isset($addres->province) ? $addres->province : '' ?>
                            <?= isset($addres->city) ? $addres->city : '' ?>
                            <?= isset($addres->area) ? $addres->area : '' ?>
                            <?= isset($value->detail_address) ? $value->detail_address : '' ?>
                        </p>
                    </div>
                <?php endforeach; ?>

            </div>
            <!-- 收货人信息  end-->

            <!-- 配送方式 start -->
            <div class="delivery">
                <h3>送货方式 </h3>


                <div class="delivery_select">
                    <table>
                        <thead>
                        <tr>
                            <th class="col1">送货方式</th>
                            <th class="col2">运费</th>
                            <th class="col3">服务信息</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach (\frontend\models\Order::$delivery as $id => $value): ?>
                            <tr <?= $id == 1 ? 'class="cur"' : '' ?>>
                                <td><input type="radio" name="delivery_id" value="<?= $id ?>"
                                        <?= $id == 1 ? 'checked="checked"' : '' ?>/><?= $value[0] ?></td>
                                <td>￥<?= $value[1] ?></td>
                                <td><?= $value[2] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>
            <!-- 配送方式 end -->

            <!-- 支付方式  start-->
            <div class="pay">
                <h3>支付方式 </h3>
                <div class="pay_select">
                    <table>
                        <?php foreach (\frontend\models\Order::$payment as $id => $value): ?>
                            <tr class="cur">
                                <td class="col1"><input type="radio" name="payment_id" value="<?= $id ?>"/><?= $value[0] ?>
                                </td>
                                <td class="col2"><?= $value[1] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <!-- 支付方式  end-->

            <!-- 发票信息 start-->
            <div class="receipt none">
                <h3>发票信息 </h3>
                <div class="receipt_select ">
                    <form action="">
                        <ul>
                            <li>
                                <label for="">发票抬头：</label>
                                <input type="radio" name="type" checked="checked" class="personal"/>个人
                                <input type="radio" name="type" class="company"/>单位
                                <input type="text" class="txt company_input" disabled="disabled"/>
                            </li>
                            <li>
                                <label for="">发票内容：</label>
                                <input type="radio" name="content" checked="checked"/>明细
                                <input type="radio" name="content"/>办公用品
                                <input type="radio" name="content"/>体育休闲
                                <input type="radio" name="content"/>耗材
                            </li>
                        </ul>
                    </form>

                </div>
            </div>
            <!-- 发票信息 end-->

            <!-- 商品清单 start -->
            <div class="goods">
                <h3>商品清单</h3>
                <table>
                    <thead>
                    <tr>
                        <th class="col1">商品</th>
                        <th class="col3">价格</th>
                        <th class="col4">数量</th>
                        <th class="col5">小计</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <?php $value = 0 ?>
                        <?php foreach ($goods as $good): ?>
                    <tr>
                        <td class="col1"><a href=""><img src="<?= $good->logo ?>" alt=""/></a> <strong><a href="">【1111购物狂欢节】<?= $good->name ?></a></strong>
                        </td>
                        <td class="col3">￥<?= $good->shop_price ?></td>
                        <td class="col4"> <?= $cart[$good->id] ?></td>
                        <td class="col5"><span>￥<?= $good->shop_price * $cart[$good->id] ?>.00</span></td>
                    </tr>
                    <?php $value += $good->shop_price * $cart[$good->id]; ?>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5">
                            <ul>
                                <li>
                                    <span><?= $num ?> 件商品，总商品金额：</span>
                                    <em>￥<?= $value ?>.00</em>
                                </li>
                                <!--                            <li>-->
                                <!--                                <span>返现：</span>-->
                                <!--                                <em>-￥240.00</em>-->
                                <!--                            </li>-->
                                <li>
                                    <span>运费：</span>
                                    <em>￥<?= $value[1] ?></em>
                                </li>
                                <li>
                                    <span>应付总额：</span>
                                    <em>￥<?= $value ?>.00</em>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <!-- 商品清单 end -->

        </div>

        <div class="fillin_ft">
            <button><span>提交订单</span></button>
            <p>应付总额：<strong>￥<?= $value ?>.00元</strong></p>
        </div>

</div>
</form>
<!-- 主体部分 end -->

<div style="clear:both;"></div>
<!-- 底部版权 start -->
<div class="footer w1210 bc mt15">
    <p class="links">
        <a href="">关于我们</a> |
        <a href="">联系我们</a> |
        <a href="">人才招聘</a> |
        <a href="">商家入驻</a> |
        <a href="">千寻网</a> |
        <a href="">奢侈品网</a> |
        <a href="">广告服务</a> |
        <a href="">移动终端</a> |
        <a href="">友情链接</a> |
        <a href="">销售联盟</a> |
        <a href="">京西论坛</a>
    </p>
    <p class="copyright">
        © 2005-2013 京东网上商城 版权所有，并保留所有权利。 ICP备案证书号:京ICP证070359号
    </p>
    <p class="auth">
        <a href=""><img src="/images/xin.png" alt=""/></a>
        <a href=""><img src="/images/kexin.jpg" alt=""/></a>
        <a href=""><img src="/images/police.jpg" alt=""/></a>
        <a href=""><img src="/images/beian.gif" alt=""/></a>
    </p>
</div>
<!-- 底部版权 end -->
</body>
</html>
