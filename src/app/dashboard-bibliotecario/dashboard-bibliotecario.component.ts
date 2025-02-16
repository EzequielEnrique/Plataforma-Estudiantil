import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { LoginService } from 'src/app/login.service';
import { ServiciosUsuariosService } from 'src/app/servicios-usuarios.service';
import { MatSnackBar } from '@angular/material/snack-bar';


@Component({
  selector: 'app-dashboard-bibliotecario',
  templateUrl: './dashboard-bibliotecario.component.html',
  styleUrls: ['./dashboard-bibliotecario.component.css']
})
export class DashboardBibliotecarioComponent implements OnInit {
  cambiarPss = false;
  editMode = false;
  persona: any = {};

  constructor(
    private router: Router,
    private http: HttpClient,
    private _snackBar: MatSnackBar,
    private usService: ServiciosUsuariosService,
    private loginService: LoginService
  ) {}

  ngOnInit() {
    if (!this.loginService.isAuthenticate()) {
      this.logout();
    }
    this.getUserData();
  }

  getUserData() {
    const token = localStorage.getItem('Token');

    this.http.get(`http://localhost/Final/API/personas.php?Authorization=${token}`)
      .subscribe(
        (data: any) => {
          this.persona = data;
        },
        error => {
          console.error('Error al obtener los datos del usuario', error);
        }
      );
  }

  editDatosPersonales(){
    this.usService.setUsuarioData(this.persona.idPersona, this.persona.perApellido, this.persona.perNombre, this.persona.perDni, this.persona.perMail);

    this.router.navigate(['editar-datos']);
  }

  logout() {
    localStorage.removeItem('Token');
    localStorage.removeItem('Rol');
    this.router.navigate(['/login']);
  }

  cambiarContrasena() {
    this.router.navigate(['change-password']);
  }

  gestionarAutores(): void {
    this.router.navigate(['autores-listar'])
  }
}


