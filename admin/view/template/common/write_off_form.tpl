<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <script Language="JavaScript">
            function XmlHttp()
            {
                var xmlhttp;
                try{xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");}
                catch(e)
                {
                    try {xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");} 
                    catch (E) {xmlhttp = false;}
                }
                if (!xmlhttp && typeof XMLHttpRequest!='undefined')
                {
                    xmlhttp = new XMLHttpRequest();
                }
                  return xmlhttp;
            }

            function ajax(param)
            {
                            if (window.XMLHttpRequest) req = new XmlHttp();
                            method=(!param.method ? "POST" : param.method.toUpperCase());

                            if(method=="GET")
                            {
                                           send=null;
                                           param.url=param.url+"&ajax=true";
                            }
                            else
                            {
                                           send="";
                                           for (var i in param.data) send+= i+"="+param.data[i]+"&";
                                           // send=send+"ajax=true"; // если хотите передать сообщение об успехе
                            }

                            req.open(method, param.url, true);
                            req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                            req.send(send);
                            req.onreadystatechange = function()
                            {
                                           if (req.readyState == 4 && req.status == 200) //если ответ положительный
                                           {
                                                           if(param.success)param.success(req.responseText);
                                           }
                            }
            }
            
            function showform(){
                $("#wo-form").toggle("slow", function(){});
                $("#formwo").show("slow", function(){});                
            }
            
            function findprod(){
                ajax({
                    url:"index.php?route=common/write_off/findProd&token=<?php echo $token_wo; ?>",
                    statbox:"status",
                    method:"POST",
                    data:
                    {
                        vin: document.getElementById("vin").value,
                        token: "<?php echo $token_wo; ?>"
                    },
                    success:function(data){
                            document.getElementById("prodinfo").innerHTML=data; 
                            $("#wo-form").hide("slow", function(){});
                            $("#statbox").innerHTML = '';
                            document.getElementById("client").value = '';
                            document.getElementById("city").value = '';
                            document.getElementById("quantity").value = '1';
                            document.getElementById("date").value = '';
                    }
                })
            }
            
            function saleprod(){
                ajax({
                    url:"index.php?route=common/write_off/saled&token=<?php echo $token_wo; ?>",
                    statbox:"status",
                    method:"POST",
                    data:
                    {
                        name: document.getElementById("name").innerText,
                        quan: document.getElementById("quan").innerText,
                        price: document.getElementById("price").innerText,
                        location: document.getElementById("loc").innerText,
                        client: document.getElementById("client").value,
                        city: document.getElementById("city").value,
                        quantity: document.getElementById("quantity").value,
                        sku: document.getElementById("vin").value,
                        date: document.getElementById("date").value,
                        saleprice: document.getElementById("saleprice").value,
                        reason: document.getElementById("reason").value
                    },
                    success:function(data){
                            document.getElementById("statbox").innerHTML=data; 
                            $("#formwo").toggle("slow", function(){});
                            $("#writeoff").toggle("slow", function(){});
                    }
                })
            }
        </script>
    <div class="alert alert-danger">
        <?php echo $notice; ?>
    </div>
    <div class="container-fluid well">
        <h4><?php echo $lable_vn; ?></h4>
            <input type='text' name='vin' id="vin"/>
            <button class="btn btn-success" onclick="findprod();">
                Найти
            </button>
    </div>
    <div class="col-lg-6" id="prodinfo"></div>
    <div class="col-lg-6 alert alert-success" id="wo-form" style="display: none;">
        <div id="statbox" class="well well-sm text text-success"></div>
        <div id="formwo" display='block'>
            <div class="form-group">
                <lable for="client" class="control-label">Покупатель<span style="color: #E83737;">*</span></lable>
                <input type="text" class="form-control" id="client" placeholder="Введите покупателя"/>
            </div>
            <div class="form-group">
                <lable for="city" class="control-label">Город покупателя<span style="color: #E83737;">*</span></lable>
                <input type="text" class="form-control" id="city" placeholder="Введите город покупателя"/>
            </div>
            <div class="form-group">
                <lable for="quantity" class="control-label">Количество<span style="color: #E83737;">*</span></lable>
                <input type="text" class="form-control" id="quantity" value="1"/>
            </div>
            <div class="form-group">
                <lable for="saleprice" class="control-label">Цена продажи</lable>
                <input type="text" class="form-control" id="saleprice" value=""/>
            </div>
            <div class="form-group">
                <lable for="reason" class="control-label">Причина уценки</lable>
                <input type="text" class="form-control" id="reason" value=""/>
            </div>
            <div class="form-group">
                <lable for="date" class="control-label">Дата покупки<span style="color: #E83737;">*</span></lable>
                <div class='input-group date' id='datetimepicker1'>
                    <input type='text' class="form-control" id="date" placeholder="Введите дату покупки"/>
                    <span class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span>
                </div>
            </div>
            <div class="form-group">
                    <button onclick="saleprod();" class="btn btn-success">Подтвердить</button>
            </div>
        </div>
    </div>
</div>
                <script type="text/javascript">
                    $(function () {
                        $('#datetimepicker1').datetimepicker();
                    });
                </script>
<?php echo $footer;?>