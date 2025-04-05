import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class EstudiantesService {
  private apiUrl = 'http://localhost/Final/API/estudiantes.php';

  constructor(private http: HttpClient) { }

  private getAuthParam(): string {
    return `Authorization=${localStorage.getItem('Token')}`;
  }
  
  // Esto se cambiaría por los datos obtenidos en el LOGIN
  // Método para enviar una solicitud POST para guardar un estudiante
  postEstudiante(studentData: any): Observable<any> {
    return this.http.post(`${this.apiUrl}?${this.getAuthParam()}`, studentData);
  }

  // Método para obtener una carrera por ID
  getEstudianteByDNI(dni: string): Observable<any> {
    return this.http.get(`${this.apiUrl}?perDni=${dni}&${this.getAuthParam()}`);
  }
}
