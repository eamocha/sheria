import React from 'react';
import ReactDOM from 'react-dom';
import AdvisorTimeLogsAddForm from './AdvisorTimeLogsAddForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<AdvisorTimeLogsAddForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});