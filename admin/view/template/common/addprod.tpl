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
</script>
<?php if ($status == 1) { ?>

    <div class="well well-sm" id="status">
        <form class="row" role="group" enctype="multipart/form-data" action="index.php?route=common/addprod/setPH&token=<?php echo $token_add; ?>" method="POST">
            <div class="form-group-lg col-lg-3" style="float: left;">
                <input class="form-control" name="vin" type="text" placeholder="Введите внутренний номер" />
            </div>
            <div class="form-group-lg col-lg-3" style="float: left;">
                <input class="btn btn-default" name="photo[]" type="file" multiple="true">
            </div>
            <div class="form-group-lg col-lg-3" style="float: left;">
                <input class="btn btn-success" type="submit" value="Загрузить">
            </div>
        </form>
    </div>

<?php } elseif ($status == 2) { ?>

    <div class="alert alert-success">
        <span>Фотографии загружены. Внутренний номер: <?php echo $vin; ?></span>
    </div>
    <form class="row" role="group" enctype="multipart/form-data" action="index.php?route=common/addprod/prodToDB&token=<?php echo $token_add; ?>" method="POST">
        <!------------------------------------------------------------------------------->
        <div class="form-group col-lg-3" style="float: left;">
            <select class="form-control" name="brand_id" id='brand' onchange='
                    ajax({
                                                               url:"index.php?route=common/addprod/get_model&token=<?php echo $token_add; ?>",
                                                               statbox:"status",
                                                               method:"POST",
                                                               data:
                                                               {
                                                                              brand: document.getElementById("brand").value,
                                                                              token: "<?php echo $token_add; ?>"
                                                               },
                                                              success:function(data){document.getElementById("model1").innerHTML=data;}

                                               })
                    '>
                <option selected="selected" disabled="disabled">Выберите марку</option>
                <?php foreach ($brands as $brand) { ?>
                    <option value="<?php echo $brand['val']; ?>"><?php echo $brand['name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group col-lg-3" style="float: left;" id="model1"></div>
        <div class="form-group col-lg-3" style="float: left;" id="model_row"></div>
        <div class="clearfix"></div>
        <hr/>
        <!------------------------------------------------------------------------------->
        <div class="form-group col-lg-3" style="float: left;">
            <select class="form-control" name="category_id" id='category' onchange='
                    ajax({
                                                               url:"index.php?route=common/addprod/get_podcat&token=<?php echo $token_add; ?>",
                                                               statbox:"status",
                                                               method:"POST",
                                                               data:
                                                               {
                                                                              categ: document.getElementById("category").value,
                                                                              token: "<?php echo $token_add; ?>"
                                                               },
                                                              success:function(data){document.getElementById("podcat").innerHTML=data;}

                                               })
                    '>
                <option selected="selected" disabled="disabled">Выберите категорию</option>
                <?php foreach ($category as $cat) { ?>
                  <option value="<?php echo $cat['val']; ?>"><?php echo $cat['name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group col-lg-3" style="float: left;" id="podcat"></div>
        <div class="clearfix"></div>
        <hr/>
        <!------------------------------------------------------------------------------->
        
        <div class="form-group">
            <lable>Каталожный номер:</lable>
            <input type="text" class="form-control" name="catn">
        </div>
        <div class="form-group">
            <lable>Внутренний номер:</lable>
            <input type="text" class="form-control" disabled="disabled" value="<?php echo $vin; ?>">
        </div>
        <div class="form-group">
            <lable>Примечание</lable>
            <input type="text" class="form-control" name="prim">
        </div>
        <div class="form-group">
            <lable>Цена</lable>
            <input type="text" class="form-control" name="price" value="0">
        </div>
        <div class="form-group">
            <lable>Количество на складе</lable>
            <input type="text" class="form-control" name="quant" value="1">
        </div>
        <div class="form-group">
            <lable>Ссылка на Avito</lable>
            <input type="text" class="form-control" name="avito">
        </div>
        <div class="form-group">
            <lable>Ссылка на drom</lable>
            <input type="text" class="form-control" name="drom">
        </div>
        <div class="form-group">
            <lable>Тип</lable>
            <select class="form-control" name="type">
                <option value="Б/У">Б/У</option>
                <option value="Новый">Новый</option>
            </select>
        </div>
        <div class="form-group">
            <lable>Состояние</lable>
            <select class="form-control" name="fix">
                <option value="Отличное">Отличное</option>
                <option value="Хорошее">Хорошее</option>
                <option value="Повреждения">Повреждения</option>
            </select>
        </div>
        <div class="form-group">
            <lable>Склад</lable>
            <input type="text" class="form-control" name="sklad">
        </div>
        <div class="form-group">
            <lable>Стеллаж</lable>
            <input type="text" class="form-control" name="stell">
        </div>
        <div class="form-group">
            <lable>Ярус</lable>
            <input type="text" class="form-control" name="yarus">
        </div>
        <input type="hidden" name="vin" value="<?php echo $vin; ?>">
        <div class="form-group">
            <lable>Полка</lable>
            <input type="text" class="form-control" name="polka">
        </div>
        <div class="form-group">
            <lable>Коробка</lable>
            <input type="text" class="form-control" name="korobka">
        </div>
        <div class="form-group">
            <input type="submit" class="form-control btn btn-success">
        </div>
    </form>

<?php } else { ?>
    <div class="alert alert-success">
        <span>Товар загружен</span><br>
        <a class="btn btn-success" href="index.php?route=common/addprod&token=<?php echo $token_add; ?>">Загрузить следующий</a>
    </div>
<?php } ?>

                <!--<select type='text' value='Сохранить комментарий' onchange='
                               ajax({
                                                               url:"index.php?route=common/addprod/get_ajax&token=<?php echo $token_add; ?>",
                                                               statbox:"status",
                                                               method:"POST",
                                                               data:
                                                               {
                                                                              first_area: "1",
                                                                              second_area: "2"
                                                              },
                                                              success:function(data){document.getElementById("status").innerHTML=data;}

                                               })'
                                                               >
                    <option value="1">1</option>
                    <option value="2">2</option>
                
                </select>-->
  </div>
</div>
<?php echo $footer;?>