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
  
  
  postEstudiante(studentData: any): Observable<any> {
    return this.http.post(`${this.apiUrl}?${this.getAuthParam()}`, studentData);
  }

  
  getEstudianteByDNI(dni: string): Observable<any> {
    return this.http.get(`${this.apiUrl}?perDni=${dni}&${this.getAuthParam()}`);
  }
}
