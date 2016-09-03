/*
@功能：购物车页面js
@作者：diamondwang
@时间：2013年11月14日
*/

var last_click_time = new Date().getTime();

$(function(){
	
	//减少
	$(".reduce_num").click(function(){
		var amount = $(this).parent().find(".amount");
		if (parseInt($(amount).val()) <= 1){
			alert("商品数量最少为1");
		} else{
			$(amount).val(parseInt($(amount).val()) - 1);
		}
		//小计
		var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(amount).val());
		$(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
		//总计金额
		var total = 0;
		$(".col5 span").each(function(){
			total += parseFloat($(this).text());
		});

		$("#total").text(total.toFixed(2));
	});

	//增加
	$(".add_num").click(function(){
		// 如果上次点击在0.5秒之内
		var clickTime = new Date().getTime();
		// 0.5秒点击一次
		if(clickTime - last_click_time <= 1500)
			return false;
		// 更新点击时间
		last_click_time = clickTime;
		
		var amount = $(this).parent().find(".amount");
		$(amount).val(parseInt($(amount).val()) + 1);
		// 从按钮所在的TR标签上获取ID
		var tr = $(this).parent().parent(); // 获取按钮所在的tr
		var gid = tr.attr("goods_id");
		var gaid = tr.attr("goods_attr_id");
		// 从按钮前面的input中获取要修改的数量
		var gn = $(this).prev("input").val();
		// 执行AJAX更新服务器的数据
		ajaxEditGoodsNumber(gid, gaid, gn);
		//小计
		var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(amount).val());
		$(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
		//总计金额
		var total = 0;
		$(".col5 span").each(function(){
			total += parseFloat($(this).text());
		});

		$("#total").text(total.toFixed(2));
	});

	//直接输入
	$(".amount").blur(function(){
		if (parseInt($(this).val()) < 1){
			alert("商品数量最少为1");
			$(this).val(1);
		}
		//小计
		var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(this).val());
		$(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
		//总计金额
		var total = 0;
		$(".col5 span").each(function(){
			total += parseFloat($(this).text());
		});

		$("#total").text(total.toFixed(2));

	});
});