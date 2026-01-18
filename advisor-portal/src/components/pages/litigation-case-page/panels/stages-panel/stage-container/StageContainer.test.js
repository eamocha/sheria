import React from 'react';
import ReactDOM from 'react-dom';
import StageContainer from './StageContainer';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<StageContainer />, div);
  ReactDOM.unmountComponentAtNode(div);
});