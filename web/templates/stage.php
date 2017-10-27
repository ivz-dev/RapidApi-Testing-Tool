<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Rapid API | Testing tool</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="/templates/css/bootstrap.css" media="screen">
    <link rel="stylesheet" href="/templates/css/custom.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,900" rel="stylesheet">
    <link rel="stylesheet" href="/templates/css/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/templates/css/main.css">
    <script src="/templates/js/vue.js"></script>
    <script src="/templates/js/vue2-filters.min.js"></script>

</head>
<body>


<div class="main-container" id="root">
    
    <div class="overlay-block-blade" v-if ="alertVisible">
    </div>
    <div id="map-block" v-bind:class="{mapActive : isMapActive}">
        <span class="map-label">Choose Location</span>
        <div id='map_canvas'></div>
        <span class="map-label" v-on:click="isMapActive = false; setCoordinates(currentMapElement);">Save</span>
    </div>

    <div class="row wrapper">
        <div class="blade-alert" v-show ="alertVisible" style="display: none">
            <div class="panel panel-primary blade-alert-content">
                <div class="panel-body">
                    Hey,
                    you have entered the package in semi-automatic testing mode. <br/>
                    <b>Have fun</b> <i class="fa fa-smile-o" aria-hidden="true"></i>
                </div>
            </div>
        </div>


        <div class="col-md-2 blocks-sidebar">
            <h4><i class="fa fa-bars" aria-hidden="true" @click="linkBlockStatus = !linkBlockStatus; overlayBlockStatus = !overlayBlockStatus"></i></h4>
            <transition name="fade">
                <ul class="link-block" v-bind:class="{ activeBlock: linkBlockStatus }">
                    <a href="/"><li>Search</li></a>
                    <a v-bind:href="prodUrl" target="_blank"><li>Production</li></a>
                    <a v-bind:href="stageUrl" target="_blank"><li>Stage</li></a>
                </ul>
            </transition>

            <ul class="blocks" >
                <transition>
                    <div class="overlay-block" v-if="overlayBlockStatus"></div>
                </transition>

                <li class="single-block">
                    <div class="search-form-sidebar row">
                        <div class="col-md-10">
                            <input type="text" class="form-control" v-model="searchInput" autofocus>
                        </div>
                        <div class="col-md-2">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </div>
                    </div>

                </li>

                <a v-for="block in filterBy(blocks, searchInput)" v-bind:href="blockUrl(packageName, block)">
                    <li v-if="block == currentBlock" class="single-block highlights">
                        {{ block }}
                    </li>

                    <li v-else class="single-block">
                        {{ block }}
                    </li>
                </a>
            </ul>
        </div>


        <div class="col-md-3 params-block">

            <input type="file" id="file">
            <h4>{{ packageName }}</h4>
            <div class="block-description">
                {{ blockDescription }}
            </div>
            <div class="required-params">
                <h4 style="text-align: left;border: none;">Required:</h4>
                <div v-for="(field, index) in fields.required">
                    <div class="list-element" v-if="field.type == 'List'">
                        <div class="form-group">
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <div class="list-item" v-for="(item,key) in field.value">
                                <input type="text" class="form-control" v-bind:id="field.rapidName" v-model="fields.required[index].value[key]">
                                <div class="remove-button" v-on:click="deleteElement(field, key)">
                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                        <div class="add-button" v-on:click="pushElement(field)">
                            <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="string-element" v-if="field.type == 'String'">
                        <div class="form-group" >
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <input type="text" class="form-control" v-bind:id="field.rapidName" v-model="field.value">
                        </div>
                    </div>
                    <div class="string-element" v-if="field.type == 'JSON'">
                        <div class="form-group" >
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <input type="text" class="form-control" v-bind:id="field.rapidName" v-model="field.value">
                        </div>
                    </div>
                    <div class="string-element" v-if="field.type == 'credentials'">
                        <div class="form-group" >
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <input type="text" class="form-control" v-bind:id="field.rapidName" v-model="field.value">
                        </div>
                    </div>
                    <div class="string-element" v-if="field.type == 'Select'">
                        <div class="form-group" >
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <select class="form-control" v-bind:id="field.rapidName" v-model="field.value">
                                <option v-for="option in field.options">{{ option }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="string-element" v-if="field.type == 'DatePicker'">
                        <div class="form-group" >
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <input type="date" class="form-control" v-bind:id="field.rapidName" v-model="field.value">
                        </div>
                    </div>
                    <div class="string-element" v-if="field.type == 'Number'">
                        <div class="form-group" >
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <input type="number" class="form-control" v-bind:id="field.rapidName" v-model="field.value">
                        </div>
                    </div>
                    <div class="list-element" v-if="field.type == 'Array'">
                        <div class="form-group">
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <div class="list-item" v-for="(item,key) in field.value">
                                <div v-for="(val,ind) in fields.required[index].value[key]">
                                    <input type="text" class="form-control"  v-model="fields.required[index].value[key][ind]" v-bind:placeholder="ind">
                                </div>

                                <div class="remove-button" v-on:click="deleteElement(field, key)">
                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                </div>
                            </div>

                        </div>


                        <div class="add-button" v-on:click="pushObject(field)">
                            <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="string-element" v-if="field.type == 'Map'">
                        <div class="form-group coordinates" >
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <input type="text" class="form-control" v-bind:id="field.rapidName" v-model="field.value">
                            <div class="location-icon" v-bind:name="field.rapidName" v-on:click="isMapActive = true; currentMapElement = field">
                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                    <div class="string-element" v-if="field.type == 'File'">
                        <div class="form-group file-block" >
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <input type="text" class="form-control" v-bind:id="field.rapidName" v-model="field.value">
                            <div class="file-upload" v-bind:name="field.rapidName" v-on:click="fileUploadFunc(); currentFileElement = field">
                                <i class="fa fa-file" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                    <div class="field-description">
                        {{ field.description }}
                    </div>
                </div>
            </div>
            <div class="optional-params">
                <h4 style="text-align: left; border: none;" v-if="showOptional">Optional:</h4>
                <div v-for="(field, index) in fields.optional">
                    <div class="list-element" v-if="field.type == 'List'">
                        <div class="form-group">
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <div class="list-item" v-for="(item,key) in field.value">
                                <input type="text" class="form-control" v-bind:id="field.rapidName" v-model="fields.optional[index].value[key]">
                                <div class="remove-button" v-on:click="deleteElement(field, key)">
                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                        <div class="add-button" v-on:click="pushElement(field)">
                            <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="string-element" v-if="field.type == 'String'">
                        <div class="form-group" >
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <input type="text" class="form-control" v-bind:id="field.rapidName" v-model="field.value">
                        </div>
                    </div>
                    <div class="string-element" v-if="field.type == 'JSON'">
                        <div class="form-group" >
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <input type="text" class="form-control" v-bind:id="field.rapidName" v-model="field.value">
                        </div>
                    </div>
                    <div class="string-element" v-if="field.type == 'credentials'">
                        <div class="form-group" >
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <input type="text" class="form-control" v-bind:id="field.rapidName" v-model="field.value">
                        </div>
                    </div>
                    <div class="string-element" v-if="field.type == 'Select'">
                        <div class="form-group" >
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <select class="form-control" v-bind:id="field.rapidName" v-model="field.value">
                                <option v-for="option in field.options">{{ option }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="string-element" v-if="field.type == 'DatePicker'">
                        <div class="form-group" >
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <input type="date" class="form-control" v-bind:id="field.rapidName" v-model="field.value">
                        </div>
                    </div>
                    <div class="string-element" v-if="field.type == 'Number'">
                        <div class="form-group" >
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <input type="number" class="form-control" v-bind:id="field.rapidName" v-model="field.value">
                        </div>
                    </div>
                    <div class="list-element" v-if="field.type == 'Array'">
                        <div class="form-group">
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <div class="list-item" v-for="(item,key) in field.value">
                                <div v-for="(val,ind) in fields.optional[index].value[key]">
                                    <input type="text" class="form-control"  v-model="fields.optional[index].value[key][ind]" v-bind:placeholder="ind">
                                </div>

                                <div class="remove-button" v-on:click="deleteElement(field, key)">
                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                </div>
                            </div>

                        </div>


                        <div class="add-button" v-on:click="pushObject(field)">
                            <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="string-element" v-if="field.type == 'Map'">
                        <div class="form-group coordinates" >
                            <label v-bind:for="field.rapidName">{{field.rapidName}}</label>
                            <input type="text" class="form-control" v-bind:id="field.rapidName" v-model="field.value">
                            <div class="location-icon" v-bind:name="field.rapidName" v-on:click="isMapActive = true; currentMapElement = field">
                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                    <div class="field-description">
                        {{ field.description }}
                    </div>
                </div>

                <br><br><br><br><br>
            </div>



            <div class="run-button col-md-3">
                TEST Function
            </div>
        </div>

        <div class="col-md-7 row-custom">
            <div class="row row-custom">
                <div class="rapid-block col-md-6">



                    <div class="preloader">
                        <img src="/templates/img/preloader.gif">
                    </div>
                    <h4>RapidAPI</h4>

                    <ul class="tabs">
                        <li class="request-tab active">Request</li>
                        <li class="response-tab" style="display: none">Response</li>
                    </ul>

                    <div class="rapid-response-content" >
                        <div class="row request-row">
                            <div class="col-md-2 method">
                            </div>

                            <div class="col-md-8 url">
                                <input type="text" class="url-input" v-model="rapidRequest.url">
                            </div>

                            <div class="col-md-2 status">
                            </div>
                        </div>

                        <div class="span-label headers-button">Headers <i class="fa fa-angle-down" aria-hidden="true"></i></div>
                        <div class="headers-block">
                            <table class="table table-condensed">
                                <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Value</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr>
                                    <td></td>
                                    <td></td>
                                </tr>

                                </tbody>
                            </table>
                        </div>

                        <div class="span-label">Body</div>

                        <pre class="prettyprint code-textarea"></pre>
                    </div>

                    <div class="rapid-request-content">
                        <div class="row request-row">
                            <textarea name="rapidRequestBody" class="code-textarea" id="rapidRequestBody" rows="20">{{ rapidRequest }}</textarea>

                            <div class="span-label">Body:</div>
                            <textarea name="rapidRequestParams" class="code-textarea" id="rapidRequestParams" rows="20">{{ rapidParams(fields) }}</textarea>
                        </div>
                    </div>

                </div>
                <div class="vendor-block col-md-6">
                    <div class="preloader">
                        <img src="/templates/img/preloader.gif">
                    </div>
                    <h4>Vendor</h4>

                    <ul class="tabs">
                        <li class="request-tab active">Request</li>
                        <li class="response-tab" style="display: none">Response</li>
                    </ul>

                    <div class="vendor-response-content" >
                        <div class="row request-row">
                            <div class="col-md-2 method">

                            </div>

                            <div class="col-md-8 url">
                                <input type="text" class="url-input" v-model="vendorRequest.url">
                            </div>

                            <div class="col-md-2 status">

                            </div>
                        </div>

                        <div class="span-label headers-button">Headers <i class="fa fa-angle-down" aria-hidden="true"></i></div>
                        <div class="headers-block">
                            <table class="table table-condensed">
                                <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Value</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr>
                                    <td></td>
                                    <td></td>
                                </tr>

                                </tbody>
                            </table>
                        </div>

                        <div class="span-label">Body</div>

                        <pre class="prettyprint code-textarea"></pre>
                    </div>

                    <div class="vendor-request-content">
                        <div class="row request-row">
                            <div class="vendor-interactive">
                                <div class="form-group">
                                    <label for="vendorUrl">Vendor url: </label>
                                    <input type="text" id="vendorUrl" class="form-control" v-model="vendorBladeMode.url">
                                </div>

                                <div class="form-group" >
                                    <label for="vendorMethod">Method: </label>
                                    <select class="form-control" v-model="vendorBladeMode.activeMethod">
                                        <option v-for="method in vendorBladeMode.methodList">{{method}}</option>
                                    </select>
                                </div>

                                <div class="param-group">
                                    <h4>Parameters </h4> <i class="fa fa-caret-down" aria-hidden="true"></i> <br>

                                    <div class="list-item keyValue" v-for="(field, index) in vendorBladeMode.parameters">
                                        <input type="text" class="form-control" v-model="field.name">
                                        <input type="text" class="form-control" v-model="field.value">
                                        <div class="remove-button" v-on:click="deleteBladeParam(vendorBladeMode.parameters, index)">
                                            <i aria-hidden="true" class="fa fa-minus-square-o"></i>
                                        </div>
                                    </div>

                                    <div class="add-button" v-on:click="pushBladeParam(vendorBladeMode.parameters)">
                                        <i aria-hidden="true" class="fa fa-plus-square-o"></i>
                                    </div>
                                </div>



                                <div class="body-group">
                                    <h4>Body </h4>  <i class="fa fa-caret-down" aria-hidden="true"></i> <br>
                                    <select class="form-control" v-model="vendorBladeMode.body.type">
                                        <option v-for="type in vendorBladeMode.bodyType">{{type}}</option>
                                    </select>

                                    <div v-if="vendorBladeMode.body.type === 'form_params'">
                                        <div class="list-item keyValue" v-for="(field, index) in vendorBladeMode.body.params">
                                            <input type="text" class="form-control" v-model="field.name">
                                            <input type="text" class="form-control" v-model="field.value">
                                            <div class="remove-button" v-on:click="deleteBladeParam(vendorBladeMode.body.params, index)">
                                                <i aria-hidden="true" class="fa fa-minus-square-o"></i>
                                            </div>
                                        </div>

                                        <div class="add-button" v-on:click="pushBladeParam(vendorBladeMode.body.params)">
                                            <i aria-hidden="true" class="fa fa-plus-square-o"></i>
                                        </div>
                                    </div>


                                    <div v-else-if="vendorBladeMode.body.type === 'json' || vendorBladeMode.body.type === 'body'">
                                        <textarea class="code-textarea" rows="15" v-model="vendorBladeMode.rawBody"></textarea>
                                    </div>

                                </div>



                                <div class="header-group">
                                    <h4>Headers </h4>  <i class="fa fa-caret-down" aria-hidden="true"></i> <br>
                                    <div class="list-item keyValue" v-for="(field, index) in vendorBladeMode.headers">
                                        <input type="text" class="form-control" v-model="field.name">
                                        <input type="text" class="form-control" v-model="field.value">
                                        <div class="remove-button" v-on:click="deleteBladeParam(vendorBladeMode.headers, index)">
                                            <i aria-hidden="true" class="fa fa-minus-square-o"></i>
                                        </div>
                                    </div>

                                    <div class="add-button" v-on:click="pushBladeParam(vendorBladeMode.headers)">
                                        <i aria-hidden="true" class="fa fa-plus-square-o"></i>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div style="display: none !important;">
                        <div class="row request-row">
                            <textarea name="vendorRequestBody" class="code-textarea" id="vendorRequestBody" rows="20">{{ vendorRequest }}</textarea>

                            <div class="span-label">Body:</div>
                            <textarea name="vendorRequestParams" class="code-textarea" id="vendorRequestParams" rows="20">{{ vendorRequestBody }}</textarea>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
<script>
    var data = <?= json_encode($data) ?>;
    if(data.fields == undefined){data.fields = [];}
    data.isMapActive = false;
    data.currentMapElement = "";
    data.currentFileElement = "";
    data.linkBlockStatus = false;
    data.overlayBlockStatus = false;
    data.searchInput = "";
    var coordinates;

    data.vendorBladeMode = {
      "url":"",
      "methodList":[
          "get",
          "post",
          "put",
          "patch",
          "delete"
      ],
      "activeMethod":"",
      "bodyType": [
        "form_params",
        "json",
        "body"
      ],
      "parameters":[],
      "body": {
          "type":"",
          "params":[]
      },
      "rawBody":"{}",
      "headers":[]
    };

    data.vendorRequestTemplate = {
        "url": "",
        "method": "",
        "requestParameters": []
    };

    if(data.currentBlock == data.blocks[0]){
        data.alertVisible = true;
    } else {
        data.alertVisible = false;
    }



    var vm = new Vue({
        el: "#root",
        data: data,
        computed: {
            prodUrl: function () {
                return "https://rapidapi.com/package/" + this.packageName;
            },
            stageUrl: function () {
                return "https://rapidapi.xyz/package/" + this.packageName;
            },
          vendorRequest: function () {
              var template = this.vendorRequestTemplate;
              template.url = this.vendorBladeMode.url;
              template.method = this.vendorBladeMode.activeMethod;
              template.requestParameters = [];
              template.headers = [];

              if(this.vendorBladeMode.parameters.length > 0){
                  var queryObj = {
                      "type" : "query",
                      "parameters" : []
                  };

                  for (var i=0; i<this.vendorBladeMode.parameters.length; i++) {
                      queryObj.parameters.push(this.vendorBladeMode.parameters[i].name);
                  }

                  if(template.requestParameters.length>0){
                      if(template.requestParameters[0].type == "query"){
                          template.requestParameters[0] = queryObj;
                      } else {
                          template.requestParameters[1] = queryObj;
                      }
                  } else {
                      template.requestParameters[0] = queryObj;
                  }

              }
              if(this.vendorBladeMode.body.params.length > 0){
                  var bodyObj = {
                      "type" : "",
                      "parameters" : []
                  };

                  bodyObj.type = this.vendorBladeMode.body.type;

                  for (var k=0; k<this.vendorBladeMode.body.params.length; k++) {
                      bodyObj.parameters.push(this.vendorBladeMode.body.params[k].name);
                  }

                  if(template.requestParameters.length>0){
                      if(template.requestParameters[0].type != "query"){
                          template.requestParameters[0] = bodyObj;
                      } else {
                          template.requestParameters[1] = bodyObj;
                      }
                  } else {
                      template.requestParameters[0] = bodyObj;
                  }

              }
              if(this.vendorBladeMode.headers.length > 0){
                  var headers = {};
                  for (var c=0; c<this.vendorBladeMode.headers.length; c++) {
                      var headerName = this.vendorBladeMode.headers[c].name;
                      var headerValue = this.vendorBladeMode.headers[c].value;
                      headers[headerName] = headerValue;
                  }

                  template.headers = headers;
              }

              return template;
          },
          vendorRequestBody: function() {
              var body = {};

              if(this.vendorBladeMode.parameters.length > 0){
                  for (var i=0; i<this.vendorBladeMode.parameters.length; i++) {
                      var paramName = this.vendorBladeMode.parameters[i].name;
                      var paramValue = this.vendorBladeMode.parameters[i].value;
                      body[paramName] = paramValue;
                  }
              }

              if(this.vendorBladeMode.body.params.length > 0){
                  for (var k=0; k<this.vendorBladeMode.body.params.length; k++) {
                      var paramName = this.vendorBladeMode.body.params[k].name;
                      var paramValue = this.vendorBladeMode.body.params[k].value;
                      body[paramName] = paramValue;
                  }
              }

              if(this.vendorBladeMode.body.type == 'json'){
                  try {
                      body.json = JSON.parse(this.vendorBladeMode.rawBody);
                  } catch(e) {
                      body.json = ""
                  }
              }

              if(this.vendorBladeMode.body.type == 'body'){
                  body.body = this.vendorBladeMode.rawBody;
              }

              return body;
          },
            showOptional: function () {
                if(this.fields.optional){
                    return true;
                } else {
                    return false;
                }
            }
        },
        methods: {
            blockUrl: function(packageName,block){
                value = "/" + packageName + "/" + block;
                return value;
            },
            vendorParams: function (fields) {
                var object = {};

                if(fields.required != undefined){
                    fields.required.map(function(value, key) {
                        if(value.value!=''){
                            object[value.vendorName] = value.value;
                        }
                    });
                }

                if(fields.optional != undefined){
                    fields.optional.map(function(value, key) {
                        if(value.value!=''){
                            object[value.vendorName] = value.value;
                        }
                    });
                }
                return object;
            },
            rapidParams: function (fields) {
                var object = {};

                if(fields.required != undefined){
                    fields.required.map(function(value, key) {
                        if(value.type == 'Array'){

                        }
                        if(value.value!=''){
                            object[value.rapidName] = value.value;
                        }
                    });
                }

                if(fields.optional != undefined){
                    fields.optional.map(function(value, key) {
                        if(value.value!=''){
                            object[value.rapidName] = value.value;
                        }
                    });
                }


                return object;
            },
            pushElement: function (field) {
                field.value.push('');
            },
            deleteElement: function (field,key) {
                field.value.splice(key,1);
            },
            pushObject: function (field) {
                var object = {};
                for(key in field.structure){
                    object[key] = "";
                }
                field.value.push(object);
            },
            activateMap: function () {
                var d = document.getElementById("map-block");
                d.className += " mapActive";
            },
            setCoordinates: function (currentMapElement) {
                currentMapElement.value = coordinates;
            },
            fileUploadFunc: function () {
                document.getElementById("file").click();
            },
            pushBladeParam: function (field) {
                field.push( {"name":"","value":""});
            },
            deleteBladeParam: function (field,key) {
                field.splice(key,1);
            }
        }
    });

</script>

<script src="/templates/js/jquery-1.10.2.min.js"></script>
<script src="/templates/js/bootstrap.min.js"></script>
<script src="/templates/js/custom.js"></script>
<script src="/templates/js/testing.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCY7RaI7vwhrStiKnWGrKTriDXEkUHgVJ8">
</script>

<script>
    var map = new google.maps.Map(document.getElementById('map_canvas'), {
        zoom: 10,
        center: new google.maps.LatLng(35.137879, -82.836914),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var myMarker = new google.maps.Marker({
        position: new google.maps.LatLng(47.651968, 9.478485),
        draggable: true
    });

    google.maps.event.addListener(myMarker, 'dragend', function (evt) {
        coordinates = evt.latLng.lat().toFixed(3) + ', ' + evt.latLng.lng().toFixed(3);
    });


    map.setCenter(myMarker.position);
    myMarker.setMap(map);


    var dataFile = new FormData();


    $("#file").change(function() {
        jQuery.each(jQuery('#file')[0].files, function(i, file) {
            dataFile.append(i, file);

            jQuery.ajax({
                url: '/ajax/uploadFile',
                data: dataFile,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function(response){
                    data.currentFileElement.value = response;
                }
            });

        });
    });
    
    $(document).ready(function () {
        setTimeout(function() {
            $(".overlay-block-blade").addClass("overlay-block-out");
            $(".blade-alert").css("display","none");
        }, 1500);
    })

</script>


</html>
