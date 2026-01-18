import React from 'react';
import ReactDOM from 'react-dom';
import ActiveTimersForm from './ActiveTimersForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ActiveTimersForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});