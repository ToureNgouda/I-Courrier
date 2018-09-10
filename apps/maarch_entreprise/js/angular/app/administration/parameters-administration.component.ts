import { ChangeDetectorRef, Component, OnInit, ViewChild } from '@angular/core';
import { MediaMatcher } from '@angular/cdk/layout';
import { HttpClient } from '@angular/common/http';
import { LANG } from '../translate.component';
import { NotificationService } from '../notification.service';
import { MatPaginator, MatTableDataSource, MatSort } from '@angular/material';

declare function $j(selector: any): any;

declare var angularGlobals: any;


@Component({
    templateUrl: "../../../../Views/parameters-administration.component.html",
    providers: [NotificationService]
})
export class ParametersAdministrationComponent implements OnInit {
    mobileQuery: MediaQueryList;
    private _mobileQueryListener: () => void;
    coreUrl: string;
    lang: any = LANG;

    parameters: any = {};

    loading: boolean = false;

    displayedColumns = ['id', 'description', 'value', 'actions'];
    dataSource: any;
    @ViewChild(MatPaginator) paginator: MatPaginator;
    @ViewChild(MatSort) sort: MatSort;


    constructor(changeDetectorRef: ChangeDetectorRef, media: MediaMatcher, public http: HttpClient, private notify: NotificationService) {
        $j("link[href='merged_css.php']").remove();
        this.mobileQuery = media.matchMedia('(max-width: 768px)');
        this._mobileQueryListener = () => changeDetectorRef.detectChanges();
        this.mobileQuery.addListener(this._mobileQueryListener);
    }

    ngOnDestroy(): void {
        this.mobileQuery.removeListener(this._mobileQueryListener);
    }

    updateBreadcrumb(applicationName: string) {
        if ($j('#ariane')[0]) {
            $j('#ariane')[0].innerHTML = "<a href='index.php?reinit=true'>" + applicationName + "</a> > <a onclick='location.hash = \"/administration\"' style='cursor: pointer'>" + this.lang.administration + "</a> > " + this.lang.parameters;
        }
    }

    applyFilter(filterValue: string) {
        filterValue = filterValue.trim(); // Remove whitespace
        filterValue = filterValue.toLowerCase(); // MatTableDataSource defaults to lowercase matches
        this.dataSource.filter = filterValue;
    }

    ngOnInit(): void {
        this.updateBreadcrumb(angularGlobals.applicationName);
        this.coreUrl = angularGlobals.coreUrl;

        this.loading = true;

        this.http.get(this.coreUrl + 'rest/parameters')
            .subscribe((data: any) => {
                this.parameters = data.parameters;

                setTimeout(() => {
                    this.dataSource = new MatTableDataSource(this.parameters);
                    this.dataSource.paginator = this.paginator;
                    this.dataSource.sort = this.sort;
                }, 0);

                this.loading = false;
            });
    }

    deleteParameter(paramId: string) {
        let r = confirm(this.lang.deleteMsg);

        if (r) {
            this.http.delete(this.coreUrl + 'rest/parameters/' + paramId)
                .subscribe((data: any) => {
                    this.parameters = data.parameters;
                    this.dataSource = new MatTableDataSource(this.parameters);
                    this.dataSource.paginator = this.paginator;
                    this.dataSource.sort = this.sort;
                    this.notify.success(this.lang.parameterDeleted);
                }, (err) => {
                    this.notify.error(err.error.errors);
                });
        }
    }
}
