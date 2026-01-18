import React from 'react';
import ReactDOM from 'react-dom';
import AdvisorTaskPage from './AdvisorTaskPage';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<AdvisorTaskPage />, div);
  ReactDOM.unmountComponentAtNode(div);
});