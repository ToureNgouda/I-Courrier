"use strict";
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
Object.defineProperty(exports, "__esModule", { value: true });
var core_1 = require("@angular/core");
var platform_browser_1 = require("@angular/platform-browser");
var router_1 = require("@angular/router");
var http_1 = require("@angular/http");
var forms_1 = require("@angular/forms");
var app_component_1 = require("./app.component");
var profile_component_1 = require("./profile.component");
var parameter_component_1 = require("./parameter.component");
var parameters_component_1 = require("./parameters.component");
var signature_book_component_1 = require("./signature-book.component");
var AppModule = (function () {
    function AppModule() {
    }
    return AppModule;
}());
AppModule = __decorate([
    core_1.NgModule({
        imports: [
            platform_browser_1.BrowserModule,
            //DataTablesModule,
            forms_1.FormsModule,
            router_1.RouterModule.forRoot([
                { path: 'profile', component: profile_component_1.ProfileComponent },
                { path: 'administration/parameter/create', component: parameter_component_1.ParameterComponent },
                { path: 'administration/parameter/update/:id', component: parameter_component_1.ParameterComponent },
                { path: 'parameters', component: parameters_component_1.ParametersComponent },
                { path: ':basketId/signatureBook/:resId', component: signature_book_component_1.SignatureBookComponent },
                { path: '**', redirectTo: '', pathMatch: 'full' },
            ], { useHash: true }),
            http_1.HttpModule
        ],
        declarations: [app_component_1.AppComponent, profile_component_1.ProfileComponent, parameters_component_1.ParametersComponent, parameter_component_1.ParameterComponent, signature_book_component_1.SignatureBookComponent, signature_book_component_1.SafeUrlPipe],
        providers: [],
        bootstrap: [app_component_1.AppComponent]
    })
], AppModule);
exports.AppModule = AppModule;
