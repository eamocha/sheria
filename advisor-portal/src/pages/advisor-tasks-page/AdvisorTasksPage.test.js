import React from 'react';
import ReactDOM from 'react-dom';
import AdvisorTasksPage from './AdvisorTasksPage';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<AdvisorTasksPage />, div);
  ReactDOM.unmountComponentAtNode(div);
});