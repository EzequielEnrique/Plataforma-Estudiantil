import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class PrestamoService {
  private baseUrl = 'http://localhost/Final/API';

  constructor(private http: HttpClient) {}

  getPrestamos(): Observable<any> {
    return this.http.get(`${this.baseUrl}/obtenerPrestamos.php`);
  }

  createPrestamo(prestamo: any): Observable<any> {
    return this.http.post(`${this.baseUrl}/crearPrestamo.php`, prestamo);
  }

  updatePrestamo(id: number, prestamo: any): Observable<any> {
    const updatedPrestamo = { id, ...prestamo };
    return this.http.post(`${this.baseUrl}/actualizarPrestamo.php`, updatedPrestamo);
  }

  deletePrestamo(id: number): Observable<any> {
    return this.http.post(`${this.baseUrl}/eliminarPrestamo.php`, { id });
  }
}







