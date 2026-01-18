import React from 'react';
import ReactDOM from 'react-dom';
import APTimerView from './APTimerView';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APTimerView />, div);
  ReactDOM.unmountComponentAtNode(div);
});