import { HttpClient } from '@angular/common/http';
import { ActivatedRoute, Router } from '@angular/router';
import { FormControl } from '@angular/forms';
import { Observable } from 'rxjs/Observable';
import { startWith } from 'rxjs/operators/startWith';
import { map } from 'rxjs/operators/map';

declare const angularGlobals: any;

export class AutoCompletePlugin {
    coreUrl: string;
    userCtrl: FormControl;
    statusCtrl: FormControl;
    elementCtrl: FormControl;
    filteredUsers: Observable<any[]>;
    filteredElements: Observable<any[]>;
    filteredStatuses: Observable<any[]>;
    userList: any[] = [];
    elemList: any[] = [];
    statusesList: any[] = [];

    constructor(public http: HttpClient, target: any[]) {
        this.coreUrl = angularGlobals.coreUrl;

        if (target.indexOf('users') != -1) {
            this.userCtrl = new FormControl();
            this.http.get(this.coreUrl + 'rest/autocomplete/users')
                .subscribe((data: any) => {
                    this.userList = data;
                    this.filteredUsers = this.userCtrl.valueChanges
                        .pipe(
                            startWith(''),
                            map(user => user ? this.autocompleteFilterUser(user) : this.userList.slice())
                        );
                }, () => {
                    location.href = "index.php";
                });
        }
        if (target.indexOf('statuses')  != -1) {
            this.statusCtrl = new FormControl();
            this.http.get(this.coreUrl + 'rest/autocomplete/statuses')
                .subscribe((data: any) => {
                    this.statusesList = data;
                    this.filteredStatuses = this.statusCtrl.valueChanges
                        .pipe(
                            startWith(''),
                            map(status => status ? this.autocompleteFilterStatuses(status) : this.statusesList.slice())
                        );
                }, () => {
                    location.href = "index.php";
                });
        }
        if (target.indexOf('usersAndEntities') != -1) {
            this.elementCtrl = new FormControl();
            this.elemList = [];

            this.http.get(this.coreUrl + 'rest/autocomplete/users')
                .subscribe((data: any) => {
                    this.elemList = data;

                    this.http.get(this.coreUrl + 'rest/autocomplete/entities')
                        .subscribe((data: any) => {
                            this.elemList = this.elemList.concat(data);
                            this.filteredElements = this.elementCtrl.valueChanges
                                .pipe(
                                    startWith(''),
                                    map(elem => elem ? this.autocompleteFilterElements(elem) : this.elemList.slice())
                                );
                        }, () => {
                            location.href = "index.php";
                        });

                }, () => {
                    location.href = "index.php";
                });

        }
        if (target.indexOf('entities') != -1) {
            this.elementCtrl = new FormControl();
            this.elemList = [];
            this.http.get(this.coreUrl + 'rest/autocomplete/entities')
                .subscribe((data: any) => {
                    this.elemList = data;
                    this.filteredElements = this.elementCtrl.valueChanges
                        .pipe(
                            startWith(''),
                            map(elem => elem ? this.autocompleteFilterElements(elem) : this.elemList.slice())
                        );
                }, () => {
                    location.href = "index.php";
                });

        } else if (target.indexOf('visaUsers') != -1) {
            this.userCtrl = new FormControl();
            this.http.get(this.coreUrl + 'rest/autocomplete/users/visa')
                .subscribe((data: any) => {
                    this.userList = data;
                    this.filteredUsers = this.userCtrl.valueChanges
                        .pipe(
                            startWith(''),
                            map(user => user ? this.autocompleteFilterUser(user) : this.userList.slice())
                        );
                }, () => {
                    location.href = "index.php";
                });
        } else {

        }

    }
    autocompleteFilterUser(name: string) {
        return this.userList.filter(user =>
            user.idToDisplay.toLowerCase().indexOf(name.toLowerCase()) >= 0);
    }

    autocompleteFilterStatuses(name: string) {
        return this.statusesList.filter(status =>
            status.idToDisplay.toLowerCase().indexOf(name.toLowerCase()) >= 0);
    }

    autocompleteFilterElements(name: string) {
        return this.elemList.filter(elem =>
            elem.idToDisplay.toLowerCase().indexOf(name.toLowerCase()) >= 0);
    }

}