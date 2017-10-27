
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Rapid API | Testing tool</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="/templates/css/bootstrap.css" media="screen">
    <link rel="stylesheet" href="/templates/css/custom.min.css">
    <link rel="stylesheet" href="/templates/css/main.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="/templates/js/vue.js"></script>
    <script src="/templates/js/vue2-filters.min.js"></script>
</head>
<body>

<div class="main-container" id="root">
    <div class="row wrapper">
        <div class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <a href="../" class="navbar-brand">Rapid testing tool</a>
                    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="col-md-12">
                <div class="input-group search-form">
                    <div class="input-group-addon">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </div>
                    <input type="text" class="form-control" id="search" placeholder="Package name..." v-model="input" autofocus>
                </div>
                <h4>Last updated packages:</h4>

                <div class="package-list">
                    <a class="package-item" v-for="package in filterBy(packages, input)" v-bind:href="packageUrl(package)">{{package}}</a>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    var packages = <?= json_encode($data['packages']) ?>;

    var vm = new Vue({
        el: "#root",
        data: {
            packages: packages,
            input: ""
        },
        methods: {
            packageUrl: function(packageName){
                var value = "/" + packageName;
                return value;
            }
        }
    })
</script>

<script src="/templates/js/jquery-1.10.2.min.js"></script>
<script src="/templates/js/bootstrap.min.js"></script>


</body>
</html>
