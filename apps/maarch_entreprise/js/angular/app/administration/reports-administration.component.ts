import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { MediaMatcher } from '@angular/cdk/layout';
import { HttpClient } from '@angular/common/http';
import { LANG } from '../translate.component';
import { NotificationService } from '../notification.service';

declare function $j(selector: any): any;

declare var angularGlobals: any;


@Component({
    templateUrl: "../../../../Views/reports-administration.component.html",
    providers: [NotificationService]
})
export class ReportsAdministrationComponent implements OnInit {
    mobileQuery: MediaQueryList;
    private _mobileQueryListener: () => void;
    coreUrl: string;
    lang: any = LANG;

    groups: any[] = [];
    reports: any[] = [];
    selectedGroup: string = "";

    loading: boolean = false;
    loadingOptions: boolean = false;


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
            $j('#ariane')[0].innerHTML = "<a href='index.php?reinit=true'>" + applicationName + "</a> > <a onclick='location.hash = \"/administration\"' style='cursor: pointer'>Administration</a> > " + this.lang.reports;
        }
    }

    ngOnInit(): void {
        this.updateBreadcrumb(angularGlobals.applicationName);
        this.coreUrl = angularGlobals.coreUrl;

        this.loading = true;

        this.http.get(this.coreUrl + 'rest/reports/groups')
            .subscribe((data: any) => {
                this.groups = data['groups'];
                this.loadReports(0)
                this.loading = false;
            }, () => {
                location.href = "index.php";
            });
    }

    loadReports(index: any) {
        this.selectedGroup = this.groups[index].group_id;
        this.loadingOptions = true;
        this.http.get(this.coreUrl + 'rest/reports/groups/' + this.groups[index].group_id)
            .subscribe((data: any) => {
                this.reports = data['reports'];
                this.loadingOptions = false;
            }, (err) => {
                this.notify.error(err.error.errors);
            });
    }

    saveReport() {
        this.http.put(this.coreUrl + 'rest/reports/groups/' + this.selectedGroup, this.reports)
            .subscribe(() => {
                this.notify.success(this.lang.modificationSaved);
            }, (err) => {
                this.notify.error(err.error.errors);
            });
    }
}