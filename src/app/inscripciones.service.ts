import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';


@Injectable({
  providedIn: 'root'
})

export class InscripcionesService {
  private apiUrl = 'http://localhost/Final/API/inscripciones.php';
  private urlIns = "http://localhost/Final/API/inscripcionesV2.php";

  constructor(private http: HttpClient) { }

  // Método para buscar inscripciones por DNI
  buscarInscripcionesPorDNI(dni: string): Observable<any> {
    const url = `${this.apiUrl}?dni=${dni}`;
    return this.http.get(url);
  }

  // Método para buscar inscripciones por DNI
  buscarInsDNI(estDNI: string): Observable<any> {
    const url = `${this.urlIns}?estDNI=${estDNI}`;
    return this.http.get(url);
  }

  //Método para obtener Turnos
  getTurnosActivos() {
    return this.http.get<any[]>(`http://localhost/Final/API/turnos.php?turEstado=1`)
  }

  // Método para crear una inscripción con datos JSON
  crearInscripcion(datos: any): Observable<any> {
    const httpOptions = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
      })
    };

    return this.http.post<any>(this.urlIns, datos, httpOptions)
      .pipe(
        catchError(error => {
          return throwError(error);
        })
      );
  }


}
