import React from 'react';
import ReactDOM from 'react-dom';
import AdvisorTaskEditForm from './AdvisorTaskEditForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<AdvisorTaskEditForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});