import { HttpClient } from '@angular/common/http';
import { ParsedVariable } from '@angular/compiler';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

export interface Autor {
  idAutor?: number; // Asegúrate de que el nombre coincida con el de tu base de datos
  autNombre: string;
  autApellido: string;
  autFecNac: string;
  autBiografia: string;
  autFecDes: string;
  
}

@Injectable({
  providedIn: 'root'
})
export class AutoresService {
  private apiUrl = 'http://localhost/Final/API/autores.php';
 
  constructor(private http: HttpClient) { }

  getAutor(): Observable<Autor[]> {
    return this.http.get<Autor[]>(this.apiUrl);
  }

  getAutorById(id: number): Observable<Autor[]> {
    return this.http.get<Autor[]>(`${this.apiUrl}?idAutor=${id}`);
  }  
  
  addAutor(Autor: Autor): Observable<number> {
    return this.http.post<number>(this.apiUrl, Autor);
  }

  editAutor(Autor: Autor): Observable<any> {
    return this.http.put<any>(this.apiUrl, Autor);
}

  delAutor(idAutor: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}?idAutor=${idAutor}`);
  }
}
