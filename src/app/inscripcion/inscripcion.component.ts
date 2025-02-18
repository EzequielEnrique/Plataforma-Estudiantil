import { Component, OnInit } from '@angular/core';
import { EstudiantesService } from 'src/app/estudiantes.service';
import { CarrerasService } from 'src/app/carreras.service';
import { MateriasService } from 'src/app/materias.service';
import { InscripcionesService } from 'src/app/inscripciones.service';
import { NgForm } from '@angular/forms';
import { ChangeDetectorRef } from '@angular/core';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Router } from '@angular/router';
import { DataService } from 'src/app/data.service';
import { LoginService } from 'src/app/login.service';

@Component({
  selector: 'app-inscripcion',
  templateUrl: './inscripcion.component.html',
  styleUrls: ['./inscripcion.component.css']
})
export class InscripcionComponent implements OnInit {
  formulario = {
    turno: '',
    carrera: '',
  };

  mensajeInscripcion: string = '';
  
  isMateriasVisibles: boolean = false;

  
  claseBoton = 'btn-custom';
  dni: string = '';
  deshabilitarDNI: boolean = false;

  todosCamposCompletos() {
    
    return (
      this.formulario.turno
    );
  }


  
  llamadoSeleccionado: any = null;
  condicionSeleccionada: any = null
  anoCursadoSeleccionado: any = null;

  materias: any[] = [];
  carreras: any[] = [];
  turnos: any[] = [];
  selectedTurno: number | null = null;
  selectedCarrera: number = 0;
  idEstudiante: number = 1;
  idEstudianteAct: any;
  inscripciones: any[] = [];
  materiasSeleccionadas: any[] = [];
  materiasTemporales: any[] = []; 

  constructor(private estudiantesService: EstudiantesService,
    private carrerasServices: CarrerasService,
    private materiasServices: MateriasService,
    private inscripcionServices: InscripcionesService,
    private cdr: ChangeDetectorRef,
    private _snackBar: MatSnackBar,
    private router: Router,
    private dataService: DataService,
    private loginService: LoginService) { }



  ngOnInit() {
    if (this.loginService.isAuthenticate() == false) {
      localStorage.removeItem('Token');
      localStorage.removeItem('Rol');
      this.router.navigate(['/login']);
    }
    this.cargarCarreras();
    this.cargarTurnosActivos();
    
    this.inscripciones = this.dataService.getInscripciones();

    if (this.dataService.getCargado() == false) {
      this.router.navigate(['/dashboard-estudiante']);
    }

    

  }
  
  cargarTurnosActivos() {
    this.inscripcionServices.getTurnosActivos().subscribe(
      (res) => { this.turnos = res; }
    )
  }

  cargarCarreras() {
    this.carrerasServices.getCarreras().subscribe((carreras) => {
      this.carreras = carreras;
    });
  }

  cargarMaterias() {
    this.materiasServices
      .getMateriasPorCarrera(this.selectedCarrera)
      .subscribe((materias) => {
        this.materias = materias;
      });
  }

  deshabilitarCamposForm: boolean = true;

  botonMostrarMaterias() {
    if (this.selectedTurno == null) {
      this._snackBar.open('¡Por favor seleccione un Turno!', 'Cerrar', { duration: 5000 }); 
    } else {
      if (this.inscripciones && Array.isArray(this.inscripciones)) {
        
        const buscarIncripcion = this.inscripciones.filter(ins => ins.idTurnos == this.selectedTurno);

        if (buscarIncripcion.length == 0) {
          this.isMateriasVisibles = true;
          this.deshabilitarCamposForm = false;
        } else {
          this._snackBar.open('¡Ya se encuentra inscripto en este llamado!', 'Cerrar', { duration: 5000 }); 
        }
      } else {
        
        this.isMateriasVisibles = true;
        this.deshabilitarCamposForm = false;
      }

    }


  }

  actualizarLlamado(materia: any, event: any) {
    const index = this.materiasTemporales.findIndex(item => item.idAsignatura === materia.idAsignaturas);
    if (index !== -1) {
      this.materiasTemporales[index].llamadoID = event.target.value;
    }
    this.llamadoSeleccionado = event.target.value; 

  }

  actualizarCondicion(materia: any, event: any) {
    const index = this.materiasTemporales.findIndex(item => item.idAsignatura === materia.idAsignaturas);
    if (index !== -1) {
      this.materiasTemporales[index].condicionID = event.target.value;
    }
    this.condicionSeleccionada = event.target.value; 

  }

  actualizarAnoCursado(materia: any, event: any) {
    const index = this.materiasTemporales.findIndex(item => item.idAsignatura === materia.idAsignaturas);
    if (index !== -1) {
      this.materiasTemporales[index].InsAnioCursado = event.target.value;
    }
    this.anoCursadoSeleccionado = event.target.value; 
    
  }


  toggleSeleccion(materia: any) {
    materia.selected = !materia.selected;

    const index = this.materiasTemporales.findIndex(item => item.idAsignatura === materia.idAsignaturas);

    if (materia.selected && index === -1) {
      this.materiasTemporales.push({
        idAsignatura: materia.idAsignaturas,
        llamadoID: this.llamadoSeleccionado,
        condicionID: this.condicionSeleccionada,
        InsAnioCursado: this.anoCursadoSeleccionado
      });
    } else if (!materia.selected && index !== -1) {
      this.materiasTemporales.splice(index, 1);
    }
  }

  guardarInformacion() {
    
    const token = localStorage.getItem('Token');

    console.log(this.materiasTemporales);
    
    const todoCompleto: boolean = this.materiasTemporales.every(materia =>
      materia.llamadoID &&
      materia.condicionID &&
      materia.InsAnioCursado)

    if (todoCompleto === true && this.materiasTemporales.length > 0) {

      
      this.materiasSeleccionadas = this.materiasTemporales.slice();
      
      const asignaturaIDArray = this.materiasTemporales.map(item => item.idAsignatura);
      const llamadoIDArray = this.materiasTemporales.map(item => item.llamadoID);
      const condicionIDArray = this.materiasTemporales.map(item => item.condicionID);
      const insAnioCursadoArray = this.materiasTemporales.map(item => item.InsAnioCursado);

      const postData = {
        
        asignaturaID: asignaturaIDArray,
        llamadoID: llamadoIDArray,
        condicionID: condicionIDArray,
        insAnioCursado: insAnioCursadoArray,
        turno: this.selectedTurno,
        Authorization: token
      };

      

      this.inscripcionServices.crearInscripcion(postData)
        .subscribe(
          (respuesta) => {
            
            this._snackBar.open('Inscripciones realizadas correctamente', 'Cerrar', { duration: 5000 }); // Mensaje emergente
          },
          (error) => {
            console.error('Error al crear las inscripciones:', error);
            
          }
        );

      this.router.navigate(['/dashboard-estudiante']);
    } else {
      this._snackBar.open('¡Debes completar todos los campos!', 'Cerrar', { duration: 5000 }); 
    }


  }


}
