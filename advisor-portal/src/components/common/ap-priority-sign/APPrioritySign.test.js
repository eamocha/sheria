import React from 'react';
import ReactDOM from 'react-dom';
import APPrioritySign from './APPrioritySign';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APPrioritySign />, div);
  ReactDOM.unmountComponentAtNode(div);
});