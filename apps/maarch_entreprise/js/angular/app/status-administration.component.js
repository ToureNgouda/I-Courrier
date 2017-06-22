"use strict";
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
Object.defineProperty(exports, "__esModule", { value: true });
var core_1 = require("@angular/core");
var http_1 = require("@angular/http");
require("rxjs/add/operator/map");
var router_1 = require("@angular/router");
var StatusAdministrationComponent = (function () {
    function StatusAdministrationComponent(http, route, router) {
        this.http = http;
        this.route = route;
        this.router = router;
        this.mode = null;
        this.parametersList = null;
        this.parameter = {
            id: null,
            param_value_string: null,
            param_value_int: null,
            param_value_date: null,
            description: null
        };
        this.lang = "";
        this.resultInfo = "";
    }
    StatusAdministrationComponent.prototype.ngOnInit = function () {
        // this.coreUrl = angularGlobals.coreUrl;
        // this.prepareParameter();
        // this.http.get(this.coreUrl + 'rest/parameters/lang')
        // .map(res => res.json())
        // .subscribe((data) => {
        //     this.lang = data;
        // });
        // this.route.params.subscribe((params) => {
        //     if(this.route.toString().includes('update')){
        //         this.mode='update';
        //         this.paramId = params['id'];
        //         this.getParameterInfos(this.paramId);                
        //     } else if (this.route.toString().includes('create')){
        //         this.mode = 'create';
        //         this.pageTitle = '<i class=\"fa fa-wrench fa-2x\"></i> Paramètre';
        //         $j('#pageTitle').html(this.pageTitle);
        //         this.type = 'string';
        //     }
        // });
    };
    StatusAdministrationComponent.prototype.prepareParameter = function () {
        $j('#inner_content').remove();
    };
    StatusAdministrationComponent.prototype.updateBreadcrumb = function (applicationName) {
        $j('#ariane').html("<a href='index.php?reinit=true'>" + applicationName + "</a> ><a href='index.php?page=admin&reinit=true'> Administration</a> > Paramètres");
    };
    StatusAdministrationComponent.prototype.getParameterInfos = function (paramId) {
        var _this = this;
        this.http.get(this.coreUrl + 'rest/parameters/' + paramId)
            .map(function (res) { return res.json(); })
            .subscribe(function (data) {
            if (data.errors) {
                _this.resultInfo = data.errors;
                $j('#resultInfo').removeClass().addClass('alert alert-danger alert-dismissible');
                $j("#resultInfo").fadeTo(3000, 500).slideUp(500, function () {
                    $j("#resultInfo").slideUp(500);
                });
            }
            else {
                var infoParam = data;
                _this.parameter.id = infoParam[0].id;
                if (infoParam[0].param_value_string != null) {
                    _this.parameter.param_value_string = infoParam[0].param_value_string;
                    _this.type = "string";
                }
                else if (infoParam[0].param_value_int != null) {
                    _this.parameter.param_value_int = infoParam[0].param_value_int;
                    _this.type = "int";
                }
                else if (infoParam[0].param_value_date != null) {
                    _this.parameter.param_value_date = infoParam[0].param_value_date;
                    _this.type = "date";
                }
                _this.parameter.description = infoParam[0].description;
                _this.pageTitle = "<i class=\"fa fa-wrench fa-2x\"></i> Paramètre : " + _this.parameter.id;
                $j('#pageTitle').html(_this.pageTitle);
            }
        });
    };
    StatusAdministrationComponent.prototype.submitParameter = function () {
        var _this = this;
        if (this.type == 'date') {
            //Résolution bug calendrier
            this.parameter.param_value_date = $j("#param_value_date").val();
            this.parameter.param_value_int = null;
            this.parameter.param_value_string = null;
        }
        else if (this.type == 'int') {
            this.parameter.param_value_date = null;
            this.parameter.param_value_string = null;
        }
        else if (this.type == 'string') {
            this.parameter.param_value_date = null;
            this.parameter.param_value_int = null;
        }
        if (this.mode == 'create') {
            this.http.post(this.coreUrl + 'rest/parameters', this.parameter)
                .map(function (res) { return res.json(); })
                .subscribe(function (data) {
                if (data.errors) {
                    _this.resultInfo = data.errors;
                    $j('#resultInfo').removeClass().addClass('alert alert-danger alert-dismissible');
                    $j("#resultInfo").fadeTo(3000, 500).slideUp(500, function () {
                        $j("#resultInfo").slideUp(500);
                    });
                    _this.parameter.param_value_date = null;
                    _this.parameter.param_value_int = null;
                    _this.parameter.param_value_string = null;
                }
                else {
                    _this.resultInfo = _this.lang.paramCreatedSuccess;
                    $j('#resultInfo').removeClass().addClass('alert alert-success alert-dismissible');
                    $j("#resultInfo").fadeTo(3000, 500).slideUp(500, function () {
                        $j("#resultInfo").slideUp(500);
                    });
                    _this.router.navigate(['administration/parameters']);
                }
            });
        }
        else if (this.mode == "update") {
            this.http.put(this.coreUrl + 'rest/parameters/' + this.paramId, this.parameter)
                .map(function (res) { return res.json(); })
                .subscribe(function (data) {
                if (data.errors) {
                    _this.resultInfo = data.errors;
                    $j('#resultInfo').removeClass().addClass('alert alert-danger alert-dismissible');
                    $j("#resultInfo").fadeTo(3000, 500).slideUp(500, function () {
                        $j("#resultInfo").slideUp(500);
                    });
                }
                else {
                    _this.resultInfo = _this.lang.paramUpdatedSuccess;
                    $j('#resultInfo').removeClass().addClass('alert alert-success alert-dismissible');
                    $j("#resultInfo").fadeTo(3000, 500).slideUp(500, function () {
                        $j("#resultInfo").slideUp(500);
                    });
                    _this.router.navigate(['administration/parameters']);
                }
            });
        }
    };
    return StatusAdministrationComponent;
}());
StatusAdministrationComponent = __decorate([
    core_1.Component({
        templateUrl: angularGlobals['status-administrationView'],
        styleUrls: ['../../node_modules/bootstrap/dist/css/bootstrap.min.css']
    }),
    __metadata("design:paramtypes", [http_1.Http, router_1.ActivatedRoute, router_1.Router])
], StatusAdministrationComponent);
exports.StatusAdministrationComponent = StatusAdministrationComponent;