<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    

    <title>web网页下载</title>
    <link rel="shortcut icon" href="Public/style/favicon.ico"> <link href="Public/style/bootstrap.min.css" rel="stylesheet">
    <link href="Public/style/font-awesome.min.css" rel="stylesheet">
    <link href="Public/style/animate.min.css" rel="stylesheet">
    <link href="Public/style/style.min.css" rel="stylesheet"><base target="_blank">

</head>

<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
      
        <div class="row">
        	<div class="col-sm-4">
            	
            </div>
            <div class="col-sm-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>页面下载</h5>                      
                    </div>
                    <div class="ibox-content">
                        <form class="form-horizontal m-t" id="commentForm" method="post" action="/Index/post">                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label">网址：</label>
                                <div class="col-sm-8">
                                    <textarea id="ccomment" name="urls" class="form-control" required="" aria-required="true"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-3">
                                    <button class="btn btn-primary" type="submit">提交</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>              
            </div>
            <div class="col-sm-4">
            	
            </div>
        </div>
    </div>
    <script src="Public/js/jquery.min.js"></script>
    <script src="Public/js/bootstrap.min.js"></script>
    <script src="Public/js/content.min.js"></script>
    <script src="Public/js/jquery.validate.min.js"></script>
    <script src="Public/js/messages_zh.min.js"></script>
    <script src="Public/js/form-validate-demo.min.js"></script>
    <script type="text/javascript" src="Public/js/stats" charset="UTF-8"></script>
</body>

</html>