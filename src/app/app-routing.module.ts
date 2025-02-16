import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { DashboardEstudianteComponent } from './dashboard-estudiante/dashboard-estudiante.component';
import { DashboardBibliotecarioComponent } from './dashboard-bibliotecario/dashboard-bibliotecario.component';
import { InicioComponent } from './inicio/inicio.component';
import { AuthService } from './auth.service';
import { InscripcionComponent } from './inscripcion/inscripcion.component';
import { EstudiantesComponent } from './estudiantes/estudiantes.component';
import { LibrosComponent } from './libros/libros.component';
import { EditarDatosUsuarioComponent } from './editar-datos-usuario/editar-datos-usuario.component';
import { EditarDatosEstudianteComponent } from './editar-datos-estudiante/editar-datos-estudiante.component';
import { ChangePasswordComponent } from './change-password/change-password.component';
import { PrestamosComponent } from './prestamos/prestamos.component';
import { AutoresListarComponent } from 'src/app/autores/autores-listar/autores-listar.component';
import { AutoresEditarComponent } from 'src/app/autores/autores-editar/autores-editar.component';
import { AutoresCargarComponent } from 'src/app/autores/autores-cargar/autores-cargar.component';

const routes: Routes = [
  { path: '', component: InicioComponent }, 
  { path: 'login', component: LoginComponent },
  { path:'dashboard-estudiante',component:DashboardEstudianteComponent, canActivate: [AuthService], data: {role: "Estudiante"}},
  { path:'estudiantes',component:EstudiantesComponent, canActivate: [AuthService], data: {role: "Estudiante"}},
  { path:'inscripcionmesas',component:InscripcionComponent, canActivate: [AuthService], data: {role: "Estudiante"}},
  { path:'editar-datos',component:EditarDatosUsuarioComponent, canActivate: [AuthService], data: {role: "Bibliotecario"}},
  { path: 'change-password', component: ChangePasswordComponent, canActivate: [AuthService], data: {role: ["Estudiante", "Bibliotecario"]}},
  { path:'editar-datos-estudiante',component:EditarDatosEstudianteComponent, canActivate: [AuthService], data: {role: "Estudiante"}},
  { path: 'dashboard-bibliotecario', component: DashboardBibliotecarioComponent, canActivate: [AuthService], data: {role: 'Bibliotecario'}},
  { path: 'libros', component: LibrosComponent, canActivate: [AuthService], data: {role: "Bibliotecario"}},
  { path: 'prestamo', component: PrestamosComponent, canActivate: [AuthService], data: {role: ["Bibliotecario", "Estudiante"]}},
  { path: 'autores-listar', component: AutoresListarComponent, canActivate: [AuthService], data: {role: ["Estudiante", "Bibliotecario"]}},
  { path: 'autores-editar/:id', component: AutoresEditarComponent, canActivate: [AuthService], data: {role: ["Estudiante", "Bibliotecario"]}},
  { path: 'autores-cargar', component: AutoresCargarComponent, canActivate: [AuthService], data: {role: ["Estudiante", "Bibliotecario"]}},

];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {}

