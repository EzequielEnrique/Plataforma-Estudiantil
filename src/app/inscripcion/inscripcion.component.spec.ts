import { ComponentFixture, TestBed } from '@angular/core/testing';

import { InscripcionComponent } from './inscripcion.component';

describe('InscripcionComponent', () => {
  let component: InscripcionComponent;
  let fixture: ComponentFixture<InscripcionComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [InscripcionComponent]
    });
    fixture = TestBed.createComponent(InscripcionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
