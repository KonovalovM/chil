import React from 'react';
import map from 'lodash/map';
import debounce from 'lodash/debounce';
import classnames from 'classnames';
import { RestaurantIndicator, RestaurantIndicatorBar } from './RestaurantIndicator';
import { BackToMenuButton } from './BackToMenuButton';
import { BasketButton } from './BasketButton';
import { LoginButton } from './LoginButton';
import { getAsset } from '../Utilities/assetsHelper';

interface SectionBarProps<SectionData> {
  sectionsData: SectionData[];
  currentSection?: SectionData;
  menuLabel?: string;
  sectionsLayout?: 'sidebar' | 'tabs';
  updateSection: (sectionData: SectionData) => void;
}

export class NavigationBar<SectionData extends { id: string; name: string }> extends React.Component<Partial<SectionBarProps<SectionData>>, {}> {
  render() {
    const { sectionsData, currentSection, updateSection, menuLabel } = this.props;
    const sectionsLayout = this.props.sectionsLayout || 'sidebar';

    if (sectionsData !== undefined && updateSection !== undefined) {
      return (
        <div className="fixedBar">
          <div className="restaurantIndicatorNavigationWrapperMobile">
            <RestaurantIndicatorBar />
          </div>
          {this.props.children}
          <div className="navigationBar">
            { sectionsLayout === 'sidebar' ?
              <SectionSideBar sectionsData={sectionsData} currentSection={currentSection} updateSection={updateSection} menuLabel={menuLabel}/> :
              <SectionTabsBar sectionsData={sectionsData} currentSection={currentSection} updateSection={updateSection} menuLabel={menuLabel}/>
            }
            <ButtonGroup leftBorder={sectionsLayout === 'tabs'}/>
          </div>
        </div>
      );
    }
    return (
      <div className="fixedBar">
        <div className="emptyBar">
          <BackToMenuButton />
          <ButtonGroup withRestaurantIndicator={false}/>
        </div>
      </div>
    );
  }
}

interface SectionSideBarState {
  isOpen: boolean;
  modalHeight: number;
  isMobile: boolean;
}

export class SectionSideBar<SectionData extends { id: string; name: string }> extends React.Component<SectionBarProps<SectionData>, SectionSideBarState> {
  containerRef:React.RefObject<HTMLDivElement>;
  constructor(props: SectionBarProps<SectionData>) {
    super(props);
    this.state = { isOpen: false, modalHeight: 0, isMobile: false };
    this.containerRef = React.createRef<HTMLDivElement>();
    this.handleMousedown = this.handleMousedown.bind(this);
    this.handleResize = debounce(this.handleResize.bind(this));
    this.openCloseDropDownMenu = this.openCloseDropDownMenu.bind(this);
    this.updateSection = this.updateSection.bind(this);
  }

  componentDidMount() {
    window.addEventListener('resize', this.handleResize);
    document.addEventListener('mousedown', this.handleMousedown);
  }

  componentWillUnmount() {
    window.removeEventListener('resize', this.handleResize);
    document.removeEventListener('mousedown', this.handleMousedown);
  }

  updateHeight() {
    if(!this.containerRef.current) {
      return;
    }
    const container = this.containerRef.current;

    const windowWidth = window.innerWidth;
    const mobileBreakPoint = 768;
    const isMobile = windowWidth < mobileBreakPoint;

    const remainderHeightPercentage = isMobile ? 1 : 0.75;
    const windowHeight = window.innerHeight || document.documentElement.clientHeight;
    const modalVerticalPosition = container.getBoundingClientRect().top + 57;

    const remainderHeight = windowHeight - modalVerticalPosition;
    const modalHeight = Math.ceil(remainderHeight * remainderHeightPercentage) + 1;
    this.setState({modalHeight, isMobile});
  }

  handleResize() {
    this.updateHeight();
  }

  handleMousedown(event: MouseEvent) {
    if (this.containerRef.current && !this.containerRef.current.contains(event.target as HTMLDivElement)) {
      this.setState({isOpen: false});
    }
  }

  openCloseDropDownMenu(event: React.MouseEvent<HTMLButtonElement, MouseEvent>) {

    if(!this.state.isOpen) {
      this.updateHeight();
    }

    this.setState(prevState => {
      return { isOpen : !prevState.isOpen };
    });
  }

  updateSection(sectionData: SectionData) {
    return () => {
      this.props.updateSection(sectionData);
      if(this.state.isMobile) {
        this.setState({isOpen: false});
      }
    };
  }

  render() {
    const { sectionsData, currentSection } = this.props;
    const iconName = this.state.isOpen ? "close-light.svg" : "down-arrow-light.png";

    const modalHeightState = this.state.isMobile ? {height: `${this.state.modalHeight}px`} : {maxHeight: `${this.state.modalHeight}px`};

    return (
      <>
        <div className="sectionSideBar" ref={this.containerRef}>
          <button onClick={this.openCloseDropDownMenu} className={classnames("toggleButton", {openToggleButton : this.state.isOpen})}>
            <p>{this.props.menuLabel || "Menu" }</p>
            <img src={getAsset(iconName)}/>
          </button>
          {this.state.isOpen ?
            <div className="modal" style={modalHeightState}>
              <ul className='list'>
                {map(sectionsData, sectionData => (
                  <li
                    key={sectionData.id}
                    onClick={this.updateSection(sectionData)}
                    className={classnames('item', {
                      current: currentSection && sectionData.id === currentSection.id,
                    })}
                  >
                    {sectionData.name}
                  </li>
                ))}
              </ul>
            </div> :
            null
          }
        </div>
        <BackToMenuButton extraClass='onLeft'/>
      </>
    );
  }
}

export class SectionTabsBar<SectionData extends { id: string; name: string }> extends React.Component<SectionBarProps<SectionData>, {}> {
  constructor(props: SectionBarProps<SectionData>) {
    super(props);
    this.updateSection = this.updateSection.bind(this);
  }

  updateSection(sectionData: SectionData) {
    return () => this.props.updateSection(sectionData);
  }

  render() {
    const { sectionsData, currentSection } = this.props;

    return (
      <div className="sectionTabsBar">
        <nav>
          <BackToMenuButton />
          <div className="list">
            {map(sectionsData, sectionData => (
              <div
                key={sectionData.id}
                onClick={this.updateSection(sectionData)}
                className={classnames('item', {
                  current: currentSection && sectionData.id === currentSection.id,
                })}
              >
                {sectionData.name}
              </div>
            ))}
          </div>
        </nav>
      </div>
    );
  }
}

export function ButtonGroup(props : { leftBorder?:boolean, withRestaurantIndicator?: boolean}) {
  const leftBorder = props.leftBorder || false;
  const withRestaurantIndicator = props.withRestaurantIndicator === undefined || props.withRestaurantIndicator === true;

  return (
    <div className={classnames("buttonGroup", {leftBorder})}>
      {
        withRestaurantIndicator ?
        <div className="restaurantIndicatorNavigationWrapper">
          <RestaurantIndicator />
        </div>
        : null
      }
      <LoginButton />
      <BasketButton />
    </div>
  );
}
