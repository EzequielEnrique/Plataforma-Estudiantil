import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class DataService {
  private inscripcionesEstudiante: any;
  private cargado: boolean = false;

  setInscripciones(datos: any){
    this.inscripcionesEstudiante = datos;
  }

  getInscripciones(): any[]{
    return this.inscripcionesEstudiante;
  }

  setCargago(cargado: boolean){
    this.cargado = cargado;
  }

  getCargado(): boolean{
    return this.cargado;
  }

  constructor() { }
}
