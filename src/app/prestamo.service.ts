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
    const token = localStorage.getItem('Token');
    return this.http.get(`${this.baseUrl}/obtenerPrestamos.php?Token=${token}`);
  }
  

  createPrestamo(prestamo: any): Observable<any> {
    const token = localStorage.getItem('Token');
    return this.http.post(`${this.baseUrl}/crearPrestamo.php?Token=${token}`, prestamo);
  }
  

  updatePrestamo(id: number, prestamo: any): Observable<any> {
    const token = localStorage.getItem('Token');
    const updatedPrestamo = { id, ...prestamo };
    return this.http.put(`${this.baseUrl}/actualizarPrestamo.php?Token=${token}`, updatedPrestamo);
  }
  

  deletePrestamo(id: number): Observable<any> {
    const token = localStorage.getItem('Token');
    return this.http.delete(`${this.baseUrl}/eliminarPrestamo.php?Token=${token}&id=${id}`);
  }
  
}







