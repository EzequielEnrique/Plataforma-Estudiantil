import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class EstudiantesService {
  private apiUrl = 'http://localhost/Final/API/estudiantes.php';

  constructor(private http: HttpClient) { }

  // Esto se cambiaría por los datos obtenidos en el LOGIN
  // Método para enviar una solicitud POST para guardar un estudiante
  postEstudiante(studentData: any): Observable<any> {
    return this.http.post(`${this.apiUrl}`, studentData);
  }

  // Método para obtener una carrera por ID
  getEstudianteByDNI(dni: any): Observable<any> {
    return this.http.get(`${this.apiUrl}?dni=${dni}`);
  }
}
