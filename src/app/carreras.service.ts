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
    return this.http.get<any[]>(`${this.apiUrl}`);
  }

  updateCarrera(id: number, nuevoNombre: string): Observable<any> {
    const body = {id: id, carNombre: nuevoNombre,};   
       return this.http.put(`${this.apiUrl}`, body);
}
}
