import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule } from '@angular/forms'; 
import { HttpClientModule } from '@angular/common/http';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { LoginComponent } from './login/login.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { ReactiveFormsModule } from '@angular/forms';
import { HeaderComponent } from './header/header.component';
import { DashboardEstudianteComponent } from './dashboard-estudiante/dashboard-estudiante.component';
import { RouterModule } from '@angular/router';
import { InicioComponent } from './inicio/inicio.component'; 
import { MatSnackBarModule } from '@angular/material/snack-bar';
import { InscripcionComponent } from './inscripcion/inscripcion.component';
import { EstudiantesComponent } from './estudiantes/estudiantes.component';
import { DashboardBibliotecarioComponent } from './dashboard-bibliotecario/dashboard-bibliotecario.component'; 
import { FooterComponent } from './footer/footer.component';
import { LibrosComponent } from './libros/libros.component';
import { EditarDatosUsuarioComponent } from './editar-datos-usuario/editar-datos-usuario.component';
import { EditarDatosEstudianteComponent } from './editar-datos-estudiante/editar-datos-estudiante.component';
import { ChangePasswordComponent } from './change-password/change-password.component';
import { PrestamosComponent } from './prestamos/prestamos.component';
import { AutoresCargarComponent } from './autores/autores-cargar/autores-cargar.component';
import { AutoresEditarComponent } from './autores/autores-editar/autores-editar.component';
import { AutoresListarComponent } from './autores/autores-listar/autores-listar.component';
import { MatAutocompleteModule } from '@angular/material/autocomplete';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';



@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    HeaderComponent,
    DashboardBibliotecarioComponent, 
    DashboardEstudianteComponent,
    InicioComponent,
    InscripcionComponent,
    EstudiantesComponent,
    FooterComponent,
    LibrosComponent,
    EditarDatosUsuarioComponent,
    EditarDatosEstudianteComponent,
    ChangePasswordComponent,
    PrestamosComponent,
    AutoresCargarComponent,
    AutoresEditarComponent,
    AutoresListarComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    FormsModule, 
    RouterModule, 
    ReactiveFormsModule, 
    MatSnackBarModule, 
    HttpClientModule, BrowserAnimationsModule,
    MatAutocompleteModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatTableModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }

