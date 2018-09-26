import { NgModule }                             from '@angular/core';
import { BrowserModule }                        from '@angular/platform-browser';
import { BrowserAnimationsModule }              from '@angular/platform-browser/animations';
import { FormsModule }                          from '@angular/forms';
import { HttpClientModule }                     from '@angular/common/http';
import { AppMaterialModule }                    from './app-material.module';

import { CustomSnackbarComponent }              from './notification.service';
import { ConfirmModalComponent }                from './confirmModal.component';

import { AppComponent }                         from './app.component';
import { AppRoutingModule }                     from './app-routing.module';
import { AdministrationModule }                 from './administration/administration.module';

import { ProfileComponent }                     from './profile.component';
import { SignatureBookComponent, SafeUrlPipe }  from './signature-book.component';
import { SaveNumericPackageComponent }          from './save-numeric-package.component';

@NgModule({
    imports: [
        BrowserModule,
        BrowserAnimationsModule,
        FormsModule,
        HttpClientModule,
        AdministrationModule,
        AppRoutingModule,
        AppMaterialModule
    ],
    declarations: [
        AppComponent,
        ProfileComponent,
        SaveNumericPackageComponent,
        SignatureBookComponent,
        SafeUrlPipe,
        CustomSnackbarComponent,
        ConfirmModalComponent
    ],
    entryComponents: [
        CustomSnackbarComponent,
        ConfirmModalComponent
    ],
    bootstrap: [AppComponent]
})
export class AppModule { }
