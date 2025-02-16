import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class LibrosService {
  private apiUrl = 'http://localhost/Final/API';

  constructor(private http: HttpClient) {}

  getLibros(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/obtenerLibros.php`);
  }

  createLibro(libro: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/crearLibro.php`, libro);
  }

  updateLibro(libro: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/actualizarLibro.php`, libro);
  }

  deleteLibro(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/eliminarLibro.php?id=${id}`);
  }
}

