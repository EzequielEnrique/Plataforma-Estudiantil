import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AutoresCargarComponent } from './autores-cargar.component';

describe('AutoresCargarComponent', () => {
  let component: AutoresCargarComponent;
  let fixture: ComponentFixture<AutoresCargarComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [AutoresCargarComponent]
    });
    fixture = TestBed.createComponent(AutoresCargarComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
