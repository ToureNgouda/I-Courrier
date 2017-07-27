import { Component, OnInit} from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router, ActivatedRoute } from '@angular/router';

declare function $j(selector: any) : any;
declare function successNotification(message: string) : void;
declare function errorNotification(message: string) : void;

declare var angularGlobals : any;

@Component({
    templateUrl : angularGlobals['parameter-administrationView'],
    styleUrls   : ['../../node_modules/bootstrap/dist/css/bootstrap.min.css']
})
export class ParameterAdministrationComponent implements OnInit {
    coreUrl                 : string;
    pageTitle               : string;
    creationMode            : boolean       = true;
    type                    : string;
    parameter               : any           = {};
    paramDateTemp           : string;
    lang                    : any           = "";

    resultInfo              : string        = "";
    loading                 : boolean       = false;

    constructor(public http: HttpClient, private route: ActivatedRoute, private router: Router) {
    }

    ngOnInit(): void {
        this.loading = true;
        this.coreUrl = angularGlobals.coreUrl;
        this.prepareParameter();
        this.updateBreadcrumb(angularGlobals.applicationName);

        this.route.params.subscribe((params) => {
            if (typeof params['id'] == "undefined"){
                this.creationMode = true;

                this.http.get(this.coreUrl + 'rest/administration/parameters/new')
                    .subscribe((data : any) => {
                        this.lang = data.lang;
                        this.type = 'string';
                        this.pageTitle = this.lang.newParameter;

                        this.loading = false;

                    }, () => {
                        location.href = "index.php";
                    });
            } else {
                this.creationMode = false;
                this.http.get(this.coreUrl + 'rest/administration/parameters/'+params['id'])
                    .subscribe((data : any) => {
                        this.parameter = data.parameter;
                        this.lang = data.lang;
                        this.type = data.type;

                        this.loading = false;

                    }, () => {
                        location.href = "index.php";
                    }); 
            }
        });
               
    }

    prepareParameter() {
        $j('#inner_content').remove();
    }

    updateBreadcrumb(applicationName: string){
        if ($j('#ariane')[0]) {
            $j('#ariane')[0].innerHTML = "<a href='index.php?reinit=true'>" + applicationName + "</a> > <a onclick='location.hash = \"/administration\"' style='cursor: pointer'>Administration</a> > <a onclick='location.hash = \"/administration/parameters\"' style='cursor: pointer'>Paramètres</a> > Modification";
        }
    }

    onSubmit() {
        if(this.type=='date'){
            this.parameter.param_value_date = $j("#paramValue").val();
            this.parameter.param_value_int=null;
            this.parameter.param_value_string=null;
        }
        else if(this.type == 'int'){
            this.parameter.param_value_date=null;
            this.parameter.param_value_string=null;
        }
        else if (this.type == 'string'){
            this.parameter.param_value_date=null;
            this.parameter.param_value_int=null;
        }

        if(this.creationMode == true){
            this.http.post(this.coreUrl + 'rest/parameters', this.parameter)
            .subscribe((data : any) => {
                this.router.navigate(['administration/parameters']);
                successNotification(data.success);
                
            },(err) => {
                errorNotification(JSON.parse(err._body).errors);
            });
        } else if(this.creationMode == false){

            this.http.put(this.coreUrl+'rest/parameters/'+this.parameter.id,this.parameter)
            .subscribe((data : any) => {
                this.router.navigate(['administration/parameters']);
                successNotification(data.success);                         
            },(err) => {
                errorNotification(JSON.parse(err._body).errors);
            });
        }
    }
}