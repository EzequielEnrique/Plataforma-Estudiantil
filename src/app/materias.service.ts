import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class MateriasService {
  private apiUrl = 'http://localhost/Final/API/asignaturas.php';
  private token = localStorage.getItem('Token'); 

  constructor(private http: HttpClient) {}

  getMateriasPorCarrera(carreraId: number): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}?carreraID=${carreraId}&Authorization=${this.token}`).pipe(
      map((materias: any[]) => {
        return materias.map(materia => ({ ...materia, selected: false }));
      })
    );
  }
}

