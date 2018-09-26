import { NgModule }                             from '@angular/core';
import { CommonModule }                         from '@angular/common';
import { FormsModule, ReactiveFormsModule }     from '@angular/forms';
import { HttpClientModule }                     from '@angular/common/http';

import { SortPipe }                             from '../../plugins/sorting.pipe';

import { MenuComponent }                        from '../menu/menu.component';
import { MenuNavComponent }                     from '../menu/menu-nav.component';
import { MenuTopComponent }                     from '../menu/menu-top.component';
import { AppMaterialModule }                    from '../app-material.module';
import { AdministrationRoutingModule }          from './administration-routing.module';

import { AdministrationComponent }                      from './administration.component';
import { UsersAdministrationComponent, UsersAdministrationRedirectModalComponent }      from './users-administration.component';
import { GroupsAdministrationComponent, GroupsAdministrationRedirectModalComponent }    from './groups-administration.component';
import { UserAdministrationComponent, UserAdministrationRedirectModalComponent }                  from './user-administration.component';
import { GroupAdministrationComponent }                 from './group-administration.component';
import { BasketsAdministrationComponent }               from './baskets-administration.component';
import { BasketAdministrationComponent, BasketAdministrationSettingsModalComponent, BasketAdministrationGroupListModalComponent }                from './basket-administration.component';
import { EntitiesAdministrationComponent, EntitiesAdministrationRedirectModalComponent} from './entities-administration.component';
import { DiffusionModelsAdministrationComponent }       from './diffusionModels-administration.component';
import { DiffusionModelAdministrationComponent }        from './diffusionModel-administration.component';
import { DoctypesAdministrationComponent, DoctypesAdministrationRedirectModalComponent }              from './doctypes-administration.component';
import { StatusesAdministrationComponent }              from './statuses-administration.component';
import { StatusAdministrationComponent }                from './status-administration.component';
import { ActionsAdministrationComponent }               from './actions-administration.component';
import { ActionAdministrationComponent }                from './action-administration.component';
import { ParametersAdministrationComponent }            from './parameters-administration.component';
import { ParameterAdministrationComponent }             from './parameter-administration.component';
import { PrioritiesAdministrationComponent }            from './priorities-administration.component';
import { PriorityAdministrationComponent }              from './priority-administration.component';
import { ReportsAdministrationComponent }               from './reports-administration.component';
import { HistoryAdministrationComponent }               from './history-administration.component';
import { BatchHistoryAdministrationComponent }          from './batchHistory-administration.component';
import { UpdateStatusAdministrationComponent }          from './update-status-administration.component';
import { NotificationsAdministrationComponent }         from './notifications-administration.component';
import { NotificationAdministrationComponent }          from './notification-administration.component';

@NgModule({
    imports:      [
        CommonModule, 
        FormsModule,
        ReactiveFormsModule,
        HttpClientModule,
        AppMaterialModule,
        AdministrationRoutingModule
    ],
    declarations: [
        MenuComponent,
        MenuNavComponent,
        MenuTopComponent,
        AdministrationComponent,
        UsersAdministrationComponent,
        UserAdministrationComponent,
        GroupsAdministrationComponent,
        GroupAdministrationComponent,
        BasketsAdministrationComponent,
        BasketAdministrationComponent,
        DoctypesAdministrationComponent,
        EntitiesAdministrationComponent,
        StatusesAdministrationComponent,
        StatusAdministrationComponent,
        ActionsAdministrationComponent,
        ActionAdministrationComponent,
        ParametersAdministrationComponent,
        ParameterAdministrationComponent,
        PrioritiesAdministrationComponent,
        PriorityAdministrationComponent,
        ReportsAdministrationComponent,
        HistoryAdministrationComponent,
        BatchHistoryAdministrationComponent,
        UpdateStatusAdministrationComponent,
        NotificationsAdministrationComponent,
        NotificationAdministrationComponent,
        UsersAdministrationRedirectModalComponent,
        UserAdministrationRedirectModalComponent,
        EntitiesAdministrationRedirectModalComponent,
        GroupsAdministrationRedirectModalComponent,
        BasketAdministrationSettingsModalComponent,
        BasketAdministrationGroupListModalComponent,
        DoctypesAdministrationRedirectModalComponent,
        DiffusionModelsAdministrationComponent,
        DiffusionModelAdministrationComponent,
        SortPipe
    ],
    entryComponents: [
        UsersAdministrationRedirectModalComponent,
        UserAdministrationRedirectModalComponent,
        EntitiesAdministrationRedirectModalComponent,
        GroupsAdministrationRedirectModalComponent,
        BasketAdministrationSettingsModalComponent,
        BasketAdministrationGroupListModalComponent,
        DoctypesAdministrationRedirectModalComponent
    ],
})
export class AdministrationModule {}