import { Component, OnInit } from '@angular/core';
import { Data, Router } from '@angular/router';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { InscripcionesService } from 'src/app/inscripciones.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { DataService } from 'src/app/data.service';
import { LoginService } from 'src/app/login.service';
import { ServiciosUsuariosService } from 'src/app/servicios-usuarios.service'; // Servicio para interactuar con las APIs


@Component({
  selector: 'app-dashboard-estudiante',
  templateUrl: './dashboard-estudiante.component.html',
  styleUrls: ['./dashboard-estudiante.component.css']
})
export class DashboardEstudianteComponent implements OnInit {
  cambiarPss = false;
  editMode = false;
  mesasVisibles = false;
  persona: any = {}; 
  inscripciones: any = [];
  nuevaContrasena: string = ''; 


  constructor(private router: Router,
    private http: HttpClient,
    private inscripcionService: InscripcionesService,
    private _snackBar: MatSnackBar,
    private dataService: DataService,
    private usService: ServiciosUsuariosService,
    private loginService: LoginService) { }

  ngOnInit() {
    if (this.loginService.isAuthenticate() == false){
      this.logout();
    }
    this.getUserData(); // Esto obtiene los datos del usuario al iniciar el componente
  }

  btnIncripciones() {
    if (this.loginService.isAuthenticate() == false){
      this.logout();
    }
    this.dataService.setCargago(true);
    this.cargarInscripciones("inscripcionmesas");

  }

  getUserData() {
    const token = localStorage.getItem('Token'); // Obtener el token del localStorage


    this.http.get(`http://localhost/Final/API/personas.php?Authorization=${token}`)
      .subscribe(
        (data: any) => {
          this.persona = data; // Asignar los datos del usuario
        },
        error => {
          console.error('Error al obtener los datos del usuario', error);
        }
      );
  }
  

  editDatosPersonales(){
    this.usService.setUsuarioData(this.persona.idPersona, this.persona.perApellido, this.persona.perNombre, this.persona.perDni, this.persona.perMail);

    this.router.navigate(['editar-datos-estudiante']);
  }


  


  // Función para mostrar/ocultar las mesas inscritas
  toggleMesas() {
    //this.mesasVisibles = !this.mesasVisibles;
    //this.router.navigate(['/estudiantes', this.persona.perDni]);
    if (this.loginService.isAuthenticate() == false){
      this.logout();
    }
    this.dataService.setCargago(true);
    this.cargarInscripciones("estudiantes");
    
  }

  cargarInscripciones(ruta: string){
    const dni: string = this.persona.perDni;
    // Verifica que el término tenga más de 2 caracteres
    this.inscripcionService.buscarInsDNI(dni).subscribe(
      (data) => {
        this.inscripciones = data;

        if (this.inscripciones.length == 0) {
          this.router.navigate([`/${ruta}`]);
        } else {
          this.dataService.setInscripciones(this.inscripciones);
          this.router.navigate([`/${ruta}`]);
        }
      }
    );
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

  gestionarPrestamos(): void {
    this.router.navigate(['prestamo'])
  }

}




