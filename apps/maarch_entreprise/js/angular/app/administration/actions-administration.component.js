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
var http_1 = require("@angular/common/http");
var translate_component_1 = require("../translate.component");
var ActionsAdministrationComponent = (function () {
    function ActionsAdministrationComponent(http) {
        this.http = http;
        this.lang = translate_component_1.LANG;
        this.actions = [];
        this.titles = [];
        this.resultInfo = "";
        this.loading = false;
    }
    ActionsAdministrationComponent.prototype.updateBreadcrumb = function (applicationName) {
        if ($j('#ariane')[0]) {
            $j('#ariane')[0].innerHTML = "<a href='index.php?reinit=true'>" + applicationName + "</a> > <a onclick='location.hash = \"/administration\"' style='cursor: pointer'>Administration</a> > Actions";
        }
    };
    ActionsAdministrationComponent.prototype.ngOnInit = function () {
        var _this = this;
        this.coreUrl = angularGlobals.coreUrl;
        this.loading = true;
        this.updateBreadcrumb(angularGlobals.applicationName);
        $j('#inner_content').remove();
        this.http.get(this.coreUrl + 'rest/administration/actions')
            .subscribe(function (data) {
            _this.actions = data['actions'];
            _this.titles = data['titles'];
            setTimeout(function () {
                _this.table = $j('#actionsTable').DataTable({
                    "dom": '<"datatablesLeft"l><"datatablesRight"f><"datatablesCenter"p>rt<"datatablesCenter"i><"clear">',
                    "lengthMenu": [10, 25, 50, 75, 100],
                    "oLanguage": {
                        "sLengthMenu": "<i class='fa fa-bars'></i> _MENU_",
                        "sZeroRecords": _this.lang.noResult,
                        "sInfo": "_START_ - _END_ / _TOTAL_ " + _this.lang.record,
                        "sSearch": "",
                        "oPaginate": {
                            "sFirst": "<<",
                            "sLast": ">>",
                            "sNext": _this.lang.next + " <i class='fa fa-caret-right'></i>",
                            "sPrevious": "<i class='fa fa-caret-left'></i> " + _this.lang.previous
                        },
                        "sInfoEmpty": _this.lang.noRecord,
                        "sInfoFiltered": "(filtré de _MAX_ " + _this.lang.record + ")"
                    },
                    "order": [[1, "asc"]],
                    "columnDefs": [
                        { "orderable": false, "targets": 4 }
                    ],
                    "fnInitComplete": function () {
                        $j('#actionsTable').show();
                    },
                    stateSave: true
                });
                $j('.dataTables_filter input').attr("placeholder", _this.lang.search);
                $j('dataTables_filter input').addClass('form-control');
                $j(".datatablesLeft").css({ "float": "left" });
                $j(".datatablesCenter").css({ "text-align": "center" });
                $j(".datatablesRight").css({ "float": "right" });
            }, 0);
            _this.loading = false;
        }, function (err) {
            console.log(err);
            location.href = "index.php";
        });
    };
    ActionsAdministrationComponent.prototype.deleteAction = function (id) {
        var _this = this;
        var r = confirm(this.lang.deleteMsg + ' ?');
        if (r) {
            this.http.delete(this.coreUrl + 'rest/actions/' + id)
                .subscribe(function (data) {
                var list = _this.actions;
                for (var i = 0; i < list.length; i++) {
                    if (list[i].id == id) {
                        list.splice(i, 1);
                    }
                }
                _this.table.row($j("#" + id)).remove().draw();
                successNotification(data.success);
            }, function (err) {
                errorNotification(JSON.parse(err._body).errors);
            });
        }
    };
    return ActionsAdministrationComponent;
}());
ActionsAdministrationComponent = __decorate([
    core_1.Component({
        templateUrl: angularGlobals["actions-administrationView"],
        styleUrls: ['../../node_modules/bootstrap/dist/css/bootstrap.min.css']
    }),
    __metadata("design:paramtypes", [http_1.HttpClient])
], ActionsAdministrationComponent);
exports.ActionsAdministrationComponent = ActionsAdministrationComponent;