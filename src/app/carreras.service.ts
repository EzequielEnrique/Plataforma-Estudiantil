import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})

export class CarrerasService {
  private apiUrl = 'http://localhost/Final/API/carreras.php'

  constructor(private http: HttpClient) {}

  // Método para obtener todas las Carreras
  getCarreras(): Observable<any[]> {
    const token = localStorage.getItem('Token');
    return this.http.get<any[]>(`${this.apiUrl}?Token=${token}`);
  }

  updateCarrera(id: number, nuevoNombre: string): Observable<any> {
    const token = localStorage.getItem('Token');
    const body = {
      idCarreras: id,
      carNombre: nuevoNombre
    };
    return this.http.put(`${this.apiUrl}?Token=${token}`, body);
  }
}
