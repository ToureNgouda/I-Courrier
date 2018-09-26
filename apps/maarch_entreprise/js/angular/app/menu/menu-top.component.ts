import { Component, OnInit } from '@angular/core';
import { LANG } from '../translate.component';

declare function $j(selector: any) : any;
declare var angularGlobals : any;


@Component({
    selector    : "menu-top",
    templateUrl : "../../../../Views/menu-top.component.html",
})
export class MenuTopComponent implements OnInit {

    coreUrl : string;
    lang    : any       = LANG;
    user    : any       = {};


    constructor() {
    }

    ngOnInit(): void {
        this.coreUrl = angularGlobals.coreUrl;
        this.user = angularGlobals.user;
    }
}
