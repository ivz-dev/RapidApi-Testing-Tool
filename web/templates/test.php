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
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,900" rel="stylesheet">
    <script src="/templates/js/vue.js"></script>
    <script src="/templates/js/vue2-filters.min.js"></script>
</head>
<body>

<div class="main-container" id="root">

    <div id="map-block" v-bind:class="{mapActive : isMapActive}">
        <span class="map-label">Choose Location</span>
        <div id='map_canvas'></div>
        <span class="map-label" v-on:click="isMapActive = false; setCoordinates(currentMapElement);">Save</span>
    </div>

    <div class="row wrapper">
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


                <span v-for="block in filterBy(blocks, searchInput)" v-bind:href="blockUrl(packageName, block)" @click="toggleTab(block)">
                    <li v-if="block == currentBlock" class="single-block highlights">
                        {{ block }}
                    </li>

                    <li v-else class="single-block">
                        {{ block }}
                    </li>
                </span>
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
                                <select class="form-control" v-bind:id="field.rapidName" v-model="fields.required[index].value">
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
                            <li class="request-tab active" id="rapidTab">Request</li>
                            <li class="response-tab" v-show="responseContentVisible">Response</li>
                        </ul>

                        <div class="rapid-response-content" v-show="responseContentVisible">
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

                            <pre class="prettyprint code-textarea" id="rapidResponse"></pre>
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
                            <li class="request-tab active" id="vendorTab">Request</li>
                            <li class="response-tab" v-show="responseContentVisible">Response</li>
                        </ul>

                        <div class="vendor-response-content" v-show="responseContentVisible">
                            <div class="row request-row">
                                <div class="col-md-2 method">
                                    {{ vendorRequest.method }}
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

                            <pre class="prettyprint code-textarea" id="vendorResponse"></pre>
                        </div>

                        <div class="vendor-request-content">
                            <div class="row request-row">
                    <textarea name="vendorRequestBody" class="code-textarea" id="vendorRequestBody" rows="20">{{ vendorRequest }}</textarea>

                                <div class="span-label">Body:</div><textarea name="vendorRequestParams" class="code-textarea" id="vendorRequestParams" rows="20">{{ vendorParams(fields) }}</textarea>
                            </div>
                        </div>

                    </div>
            </div>
        </div>
    </div>
</div>

</body>
<script>
    var data = {};

    data.metadata = <?= json_encode($data) ?>;
    data.currentBlock = data.metadata.currentBlock;
    data.packageName = data.metadata.packageName;
    data.blocks = data.metadata.blocks;

    data.isMapActive = false;
    data.currentMapElement = "";
    data.currentFileElement = "";

    data.linkBlockStatus = false;
    data.overlayBlockStatus = false;
    data.responseContentVisible = false;

    data.searchInput = "";

    var coordinates;

    var vm = new Vue(
    {
        el: "#root",
        data: data,
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
            toggleTab: function (block) {
                this.currentBlock = block;
                document.getElementById("vendorTab").click();
                document.getElementById("rapidTab").click();
                url = '/' + this.packageName + '/' + block;
                history.pushState(null, null, url);
            }
        },
        computed: {
            prodUrl: function () {
                return "https://rapidapi.com/package/" + this.packageName + "/functions/" + this.currentBlock;
            },
            stageUrl: function () {
                return "https://rapidapi.xyz/package/" + this.packageName + "/functions/" + this.currentBlock;
            },
            showOptional: function () {
                if(this.fields.optional){
                    return true;
                } else {
                    return false;
                }
            },
            blockDescription: function () {

                return this.metadata[this.currentBlock].blockDescription;
            },
            fields: function () {
                if(this.metadata[this.currentBlock].fields != undefined){
                    return this.metadata[this.currentBlock].fields;
                } else {
                    return [];
                }

            },
            vendorRequest: function () {
                return this.metadata[this.currentBlock].vendorRequest;
            },
            rapidRequest: function () {
                return this.metadata[this.currentBlock].rapidRequest;
            }
        }
    });

</script>

<script src="/templates/js/jquery-1.10.2.min.js"></script>
<script src="/templates/js/bootstrap.min.js"></script>
<script src="/templates/js/custom.js"></script>
<script src="/templates/js/testing.js"></script>
<script src="/templates/js/localStorage.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCY7RaI7vwhrStiKnWGrKTriDXEkUHgVJ8"></script>
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

</script>
</html>
