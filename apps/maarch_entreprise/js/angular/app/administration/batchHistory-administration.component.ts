import { ChangeDetectorRef, Component, OnInit, ViewChild } from '@angular/core';
import { MediaMatcher } from '@angular/cdk/layout';
import { HttpClient } from '@angular/common/http';
import { LANG } from '../translate.component';
import { MatPaginator, MatTableDataSource, MatSort } from '@angular/material';

declare function $j(selector: any): any;

declare var angularGlobals: any;


@Component({
    templateUrl: "../../../../Views/batchHistory-administration.component.html"
})

export class BatchHistoryAdministrationComponent implements OnInit {

    private _mobileQueryListener    : () => void;
    mobileQuery                     : MediaQueryList;

    coreUrl                         : string;
    lang                            : any       = LANG;
    loading                         : boolean   = false;
    limitExceeded                   : boolean   = false;

    data                            : any[]     = [];
    
    startDate                       : Date      = new Date();
    endDate                         : Date      = new Date();

    displayedColumns    = ['event_date', 'total_processed', 'total_errors', 'info', 'module_name'];
    dataSource          = new MatTableDataSource(this.data);


    @ViewChild(MatPaginator) paginator: MatPaginator;
    @ViewChild(MatSort) sort: MatSort;
    applyFilter(filterValue: string) {
        filterValue = filterValue.trim();
        filterValue = filterValue.toLowerCase();
        this.dataSource.filter = filterValue;
    }

    constructor(changeDetectorRef: ChangeDetectorRef, media: MediaMatcher, public http: HttpClient) {
        $j("link[href='merged_css.php']").remove();

        this.startDate.setHours(0,0,0,0);
        this.startDate.setMonth(this.endDate.getMonth()-1);
        this.endDate.setHours(23,59,59,59);

        this.mobileQuery = media.matchMedia('(max-width: 768px)');
        this._mobileQueryListener = () => changeDetectorRef.detectChanges();
        this.mobileQuery.addListener(this._mobileQueryListener);
    }

    ngOnDestroy(): void {
        this.mobileQuery.removeListener(this._mobileQueryListener);
    }

    ngOnInit(): void {
        this.coreUrl = angularGlobals.coreUrl;
        this.loading = true;

        this.http.get(this.coreUrl + 'rest/batchHistories', {params: {"startDate" : (this.startDate.getTime() / 1000).toString(), "endDate" : (this.endDate.getTime() / 1000).toString()}})
            .subscribe((data: any) => {
                this.data = data['batchHistories'];
                this.limitExceeded = data['limitExceeded'];
                this.loading = false;
                setTimeout(() => {
                    this.dataSource = new MatTableDataSource(this.data);
                    this.dataSource.paginator = this.paginator;
                    this.dataSource.sort = this.sort;
                }, 0);
            }, () => {
                location.href = "index.php";
            });
    }

    refreshHistory() {
        this.startDate.setHours(0,0,0,0);
        this.endDate.setHours(23,59,59,59);
        
        this.http.get(this.coreUrl + 'rest/batchHistories', {params: {"startDate" : (this.startDate.getTime() / 1000).toString(), "endDate" : (this.endDate.getTime() / 1000).toString()}})
            .subscribe((data: any) => {
                this.data = data['batchHistories'];
                this.limitExceeded = data['limitExceeded'];
                this.loading = false;
                setTimeout(() => {
                    this.dataSource = new MatTableDataSource(this.data);
                    this.dataSource.paginator = this.paginator;
                    this.dataSource.sort = this.sort;
                }, 0);
            }, () => {
                location.href = "index.php";
            });
    }
}