import React from 'react';
import classnames from 'classnames';
import map from 'lodash/map';

const KEY_UP = 38;
const KEY_DOWN = 40;

export interface SearchBoxProps {
  submitCallback: (searchText: string) => void;
  terms: string[];
}

export interface SearchBoxState {
  searchText: string;
  suggestions: string[];
  indexSuggestion: number;
}

export class SearchBox extends React.Component<SearchBoxProps, SearchBoxState> {
  constructor(props: SearchBoxProps) {
    super(props);
    this.state = { searchText: '', suggestions: [], indexSuggestion: -1 };

    this.handleChange = this.handleChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleKeyDown = this.handleKeyDown.bind(this);
    this.onSuggestionClick = this.onSuggestionClick.bind(this);
  }

  handleChange(event: React.ChangeEvent<HTMLInputElement>) {
    const suggestions = this.filterSuggestions(event.target.value);
    this.setState({
      searchText: event.target.value,
      suggestions,
      indexSuggestion: -1,
    });
  }

  handleSubmit(event: React.SyntheticEvent) {
    event.preventDefault();
    this.props.submitCallback(this.state.searchText);
    this.setState({ suggestions: [], indexSuggestion: -1 });
  }

  handleKeyDown(event: React.KeyboardEvent) {
    const nSuggestions = this.state.suggestions.length;
    let indexSuggestion = this.state.indexSuggestion;
    if (event.keyCode !== KEY_DOWN && event.keyCode !== KEY_UP) {
      return;
    }
    if (!nSuggestions) return;

    if (event.keyCode === KEY_DOWN) {
      indexSuggestion = (indexSuggestion + 1) % nSuggestions;
    }
    if (event.keyCode === KEY_UP) {
      indexSuggestion = indexSuggestion === -1 || indexSuggestion === 0 ? nSuggestions - 1 : indexSuggestion - 1;
    }
    const searchText = this.state.suggestions[indexSuggestion];
    this.setState({ indexSuggestion, searchText });
  }

  acceptSuggestion(suggestion: string) {
    this.props.submitCallback(this.state.searchText);
    this.setState({
      searchText: suggestion,
      suggestions: [],
      indexSuggestion: -1,
    });
  }

  filterSuggestions(text: string) {
    if (text === '') {
      return [];
    }
    text = text.toLowerCase();
    const suggestions = this.props.terms;
    const results = suggestions.filter((word: string) => word.toLowerCase().includes(text));
    return results;
  }

  onSuggestionClick(suggestion: string) {
    return () => {
      this.acceptSuggestion(suggestion);
    };
  }

  render() {
    const { searchText, suggestions } = this.state;
    return (
      <div className="searchBox">
        <form onSubmit={this.handleSubmit}>
          <h2>SEARCH RESTAURANTS FOR PICKUP</h2>
          <div className="inputs">
            <div className="textInput">
              <input
                type="text"
                name="searchText"
                placeholder="City, State, or Restaurant Name"
                value={searchText}
                onChange={this.handleChange}
                onKeyDown={this.handleKeyDown}
              />
              <ul
                className={classnames('suggestions', {
                  hidden: suggestions.length === 0,
                })}
              >
                {map(suggestions, suggestion => (
                  <li key={suggestion} onClick={this.onSuggestionClick(suggestion)} className={suggestion === searchText ? 'selected' : ''}>
                    {suggestion}
                  </li>
                ))}
              </ul>
            </div>
            <input type="submit" value="Search" />
          </div>
        </form>
      </div>
    );
  }
}
