import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { ProfilePage } from './profil.page'; // Perbaikan di sini

const routes: Routes = [
  {
    path: '',
    component: ProfilePage // Perbaikan di sini
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class ProfilPageRoutingModule {}