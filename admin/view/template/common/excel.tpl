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
      <?php if (isset($success_upload)) { echo '<div class="alert alert-success"><p>'.$success_upload.'</p></div>';}?>
      <?php if (isset($broken)) { echo '<div class="alert alert-danger"><p>'.$broken.'</p></div>';}?>
      
      <?php 
        if (!empty($uper)) {
            echo '<div class="alert alert-danger">';
            foreach ($uper as $error){
                if(is_array($error)){
                    foreach($error as $err){
                        echo '<p>'.$err.'</p>';
                    }
                } else{
                    echo '<p>'.$error.'</p>';
                }
            } 
        echo '<p>В загруженном файле находится '.$uper['match'].' товаров, имеющихся в базе.</p>';
        echo '</div>';
      }?>
      
      <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-download"></i>&nbsp;Выгрузка товаров</a></li>
            <li role="presentation" class="active"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab"><i class="fa fa-upload"></i>&nbsp;Загрузка товаров</a></li>
            <li role="presentation"><a href="#sync" aria-controls="messages" role="tab" data-toggle="tab"><i class="fa fa-caret-square-o-right"></i>&nbsp;Синхронизация</a></li>
            <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab"><i class="fa fa-th-list"></i>&nbsp;Операции с файлами</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content well">
            <div role="tabpanel" class="tab-pane fade" id="home">
                <h3>Выгрузка товаров магазина</h3>
                <span class="label label-default">Скачайте на жёсткий диск полный список товаров Вашего магазина</span>
                <hr>
                <a href="index.php?route=common/excel/downloadFile&token=<?php echo $token_excel;?>" class="btn btn-block btn-danger"><i class="fa fa-download"></i>&nbsp;Выгрузка товаров</a>
             </div>
            <div role="tabpanel" class="tab-pane fade in active" id="messages">
                <h3>Загрузка товаров в магазин</h3>
                <span class="label label-default">Загрузите eXcel файл на сервер для обновления базы товаров магазина</span>
                <hr>
                <button type="button" class="btn btn-block btn-success" id="upload"><i class="fa fa-upload"></i>&nbsp;Загрузка товаров</button>
                <div class="alert alert-success" id="up_form" style="margin-top: 5px; display: none;">
                    <div class="col-lg-12">
                        <div class="col-lg-6">
                            <form class="btn-group" role="group" enctype="multipart/form-data" action="index.php?route=common/excel/upload&token=<?php echo $token_excel;?>" method="POST">
                                <input class="btn btn-default" name="userfile" type="file"/>
                                <input class="btn btn-success" type="submit" value="Отправить файл" />
                            </form>
                        </div><!-- /.col-lg-6 -->
                    </div><!-- /.row -->
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="sync">
                <h3>Синхронизация товаров и фото</h3>
                <span class="label label-default">Загрузите eXcel файл на сервер для очистки лишних фото</span>
                <hr>
                <div class="col-lg-4">
                    <button type="button" class="btn btn-block btn-success" id="synch"><i class="fa fa-upload"></i>&nbsp;Загрузка файла</button>
                    <div class="alert alert-success" id="s_form" style="margin-top: 5px; display: none;">
                        <div class="col-lg-12">
                            <div class="col-lg-6">
                                <form class="btn-group" role="group" enctype="multipart/form-data" action="index.php?route=common/excel/synch&token=<?php echo $token_excel;?>" method="POST">
                                    <input class="btn btn-default" name="userfile" type="file"/>
                                    <input class="btn btn-success" type="submit" value="Отправить файл" />
                                </form>
                            </div><!-- /.col-lg-6 -->
                        </div><!-- /.row -->
                    </div>
                </div>
                <div class="col-lg-4">
                    <a type="button" href="index.php?route=common/excel/clearPhotos&token=<?php echo $token_excel;?>" class="btn btn-block btn-warning"><i class="fa fa-dedent"></i>&nbsp;Очистить фотографии, непривязанные к товарам.</a>
                </div>
                <div class="col-lg-4">
                    <a type="button" href="index.php?route=common/excel/PhotToProd&token=<?php echo $token_excel;?>" class="btn btn-block btn-danger"><i class="fa fa-code-fork"></i>&nbsp;Привязать фотографии к товарам к товарам.</a>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="settings">
                <h3>Операции с файлами</h3>
                <span class="label label-default">Отслеживайте движение файлов в магазине</span>
                <hr>
                <div class="col-lg-12">
                    <table class="table table-striped"> 
                        <thead> 
                          <tr> 
                            <th>id</th> 
                            <th>Название документа</th> 
                            <th>Дата загрузки</th> 
                            <th>#</th> 
                          </tr> 
                        </thead> 
                        <tbody>
                            <?php
                                foreach($results_ex as $res){
                                    echo '<tr>'
                                        .'<th scope="row">'.$res['id'].'</th>'
                                        .'<td>'.$res['name'].'</td>'
                                        .'<td>'.$res['timedate'].'</td>'
                                        .'<td>'.$res['going'].'</td>'
                                        .'</tr>';
                                }
                            ?> 
                        </tbody> 
                      </table>
                </div>
            </div>
        </div>

      </div>
  </div>
</div>
<script type="text/javascript">
      $('#myTabs a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
      });
      $( "#upload" ).click(function() {
        $( "#up_form" ).animate({
          height: "toggle"
        }, 300, function() {
        });
      });
      $( "#synch" ).click(function() {
        $( "#s_form" ).animate({
          height: "toggle"
        }, 300, function() {
        });
      });
</script>
<?php echo $footer;?>