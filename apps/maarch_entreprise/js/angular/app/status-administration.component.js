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
        this.pageTitle = "";
        this.mode = null;
        this.status = {
            id: null,
            label_status: null,
            can_be_searched: null,
            can_be_modified: null,
            is_folder_status: null,
            img_filename: null
        };
        this.lang = "";
        this.statusImages = "";
        this.loading = false;
    }
    StatusAdministrationComponent.prototype.ngOnInit = function () {
        var _this = this;
        this.loading = true;
        this.coreUrl = angularGlobals.coreUrl;
        this.prepareStatus();
        this.route.params.subscribe(function (params) {
            if (_this.route.toString().includes('status/new')) {
                _this.http.get(_this.coreUrl + 'rest/status/new')
                    .map(function (res) { return res.json(); })
                    .subscribe(function (data) {
                    _this.lang = data['lang'];
                    _this.statusImages = data['statusImages'];
                    _this.mode = 'create';
                    _this.pageTitle = _this.lang.newStatus;
                    _this.updateBreadcrumb(angularGlobals.applicationName);
                });
            }
            else {
                _this.mode = 'update';
                _this.statusIdentifier = params['identifier'];
                _this.getStatusInfos(_this.statusIdentifier);
            }
            setTimeout(function () {
                $j(".help").tooltipster({
                    theme: 'tooltipster-maarch',
                    interactive: true
                });
            }, 0);
        });
        this.loading = false;
    };
    StatusAdministrationComponent.prototype.prepareStatus = function () {
        $j('#inner_content').remove();
    };
    StatusAdministrationComponent.prototype.updateBreadcrumb = function (applicationName) {
        var breadCrumb = "<a href='index.php?reinit=true'>" + applicationName + "</a> > " +
            "<a onclick='location.hash = \"/administration\"' style='cursor: pointer'>" + this.lang.admin + "</a> > " +
            "<a onclick='location.hash = \"/administration/status\"' style='cursor: pointer'>" + this.lang.admin_status + "</a> > ";
        if (this.mode == 'create') {
            breadCrumb += this.lang.newItem;
        }
        else {
            breadCrumb += this.lang.modification;
        }
        $j('#ariane')[0].innerHTML = breadCrumb;
    };
    StatusAdministrationComponent.prototype.getStatusInfos = function (statusIdentifier) {
        var _this = this;
        this.http.get(this.coreUrl + 'rest/status/' + statusIdentifier)
            .map(function (res) { return res.json(); })
            .subscribe(function (data) {
            _this.status = data['status'][0];
            if (_this.status.can_be_searched == 'Y') {
                _this.status.can_be_searched = true;
            }
            else {
                _this.status.can_be_searched = false;
            }
            if (_this.status.can_be_modified == 'Y') {
                _this.status.can_be_modified = true;
            }
            else {
                _this.status.can_be_modified = false;
            }
            if (_this.status.is_folder_status == 'Y') {
                _this.status.is_folder_status = true;
            }
            else {
                _this.status.is_folder_status = false;
            }
            _this.lang = data['lang'];
            _this.statusImages = data['statusImages'];
            _this.pageTitle = _this.lang.modify_status + ' : ' + _this.status.id;
            _this.updateBreadcrumb(angularGlobals.applicationName);
        }, function (err) {
            errorNotification(JSON.parse(err._body).errors);
        });
    };
    StatusAdministrationComponent.prototype.selectImage = function (image_name) {
        this.status.img_filename = image_name;
    };
    StatusAdministrationComponent.prototype.submitStatus = function () {
        var _this = this;
        if (this.mode == 'create') {
            this.http.post(this.coreUrl + 'rest/status', this.status)
                .map(function (res) { return res.json(); })
                .subscribe(function (data) {
                successNotification(_this.lang.newStatusAdded + ' : ' + _this.status.id);
                _this.router.navigate(['administration/status']);
            }, function (err) {
                errorNotification((JSON.parse(err._body).errors).join("<br>"));
            });
        }
        else if (this.mode == "update") {
            this.http.put(this.coreUrl + 'rest/status/' + this.statusIdentifier, this.status)
                .map(function (res) { return res.json(); })
                .subscribe(function (data) {
                successNotification(_this.lang.statusUpdated + ' : ' + _this.status.id);
                _this.router.navigate(['administration/status']);
            }, function (err) {
                errorNotification((JSON.parse(err._body).errors).join("<br>"));
            });
        }
    };
    return StatusAdministrationComponent;
}());
StatusAdministrationComponent = __decorate([
    core_1.Component({
        templateUrl: angularGlobals['status-administrationView'],
        styleUrls: ['../../node_modules/bootstrap/dist/css/bootstrap.min.css', 'css/status-administration.component.css']
    }),
    __metadata("design:paramtypes", [http_1.Http, router_1.ActivatedRoute, router_1.Router])
], StatusAdministrationComponent);
exports.StatusAdministrationComponent = StatusAdministrationComponent;
