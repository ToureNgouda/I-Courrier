import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import {Location} from '@angular/common';
import { LANG } from '../translate.component';

declare function $j(selector: any) : any;

declare var angularGlobals : any;


@Component({
    selector: 'menu-nav',
    templateUrl :   "../../../../Views/menuNav.component.html",
})
export class MenuNavComponent implements OnInit {

    lang: any = LANG;
    coreUrl                     : string;
    router :any;

    constructor(public http: HttpClient, private _location: Location, private _router: Router) {
        this.router = _router;
    }

    ngOnInit(): void {      
        this.coreUrl = angularGlobals.coreUrl;
    }

    backClicked() {
        this._location.back();
    }
}
