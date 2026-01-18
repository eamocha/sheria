import React from 'react';
import ReactDOM from 'react-dom';
import TimeTracking from './TimeTracking';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<TimeTracking />, div);
  ReactDOM.unmountComponentAtNode(div);
});